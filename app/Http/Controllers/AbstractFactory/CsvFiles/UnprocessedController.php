<?php

namespace App\Http\Controllers\AbstractFactory\CsvFiles;

use App\Http\Controllers\AbstractFactory\CsvFiles\AbstractController;
use Illuminate\Support\Facades\Storage;
use App\Clemence\Contact\IntermediateRecord;
use App\UploadedFile;
use Exception;
use App\Http\Controllers\AbstractFactory\CsvLineProcesserFactory;
use App\Http\Controllers\AbstractFactory\CsvLines\AbstractCsvParser;
use App\Jobs\SingleIntermediaryToJson;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyContactDetailsNotice;
use Illuminate\Support\Facades\Log;

/**
 * The abstract controller returns this for new and unprocessed contact 
 * files.
 */
class UnprocessedController extends AbstractController {

    /** @var ContactCsvFile */
    private $file;
    private $header;
    private $contacts;
    private $intermediaries;

    public function process(\App\ContactCsvFile $file) {
        try {
            $this->initializeDelegate();
            $this->file = $file;
            $this->parseFile();
            $this->makeIntermediaries();
            Log::info("Finished making intermediaries");
            $this->saveIntermediaries();
            Log::info("Finished saving intermediaries");
            $this->createNextJob();
            Log::info("Created next job.");
            $this->setProcessedDate();
            Log::info("Finished process date.");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Unable to process file without an exception.");
        }
        $this->cleanup();
        Log::info("Finished cleanup.");
    }

    private function cleanup() {
        unset($this->file);
        unset($this->header);
        unset($this->contacts);
        unset($this->intermediaries);
    }

    private function parseFile() {
        $content = $this->getFileContent();
        /* @var $csvParser AbstractCsvParser */
        $format = $this->file->format;
        $csvParser = CsvLineProcesserFactory::makeByFormat($format);
        list( $header, $lines ) = $csvParser->breakIntoHeaderAndContacts($content);
        $this->header = $header;
        $this->contacts = $lines;
        $this->getDelegate()->reportHeader($this->header);
    }

    private function makeIntermediaries() {
        $this->intermediaries = new \SplDoublyLinkedList();
        foreach ($this->contacts as $line) {
            try {
                $intermediary = $this->makeFromLine($line);
                $this->intermediaries->push($intermediary);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Unable to make intermediary from file line:\n$line\n");
            } catch (\Error $e) {
                \Illuminate\Support\Facades\Log::error("Unable to make intermediary from file line:\n$line\n");
            }
        }
    }

    private function setProcessedDate() {
        $upload = $this->file->upload;
        /* @var $upload UploadedFile */
        $upload->markProcessed();
        $upload->save();
    }

    private function createNextJob() {
        $user = null;
        $count = 0;
        foreach ($this->intermediaries as $intermediate) {
            try {
                $count++;
                if ($user == null) {
                    $user = \App\User::find($intermediate->user_id);
                }
                $intermediate = IntermediateRecord::find($intermediate->id);
                if ($intermediate) {
                    SingleIntermediaryToJson::dispatch($intermediate);
                }
                Log::info("Created intermediate conversion job for record #$count");
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed creating next job un UnprocessedController.");
            }
        }
        if ($user != null) {
            Mail::queue(new VerifyContactDetailsNotice($user));
        }
    }

    private function saveIntermediaries() {
        $count = count($this->intermediaries);
        $itemCount = 0;
        $start = microtime(true);
        \Illuminate\Support\Facades\Log::info("Saving $count intermediary files.");
        foreach ($this->intermediaries as $item) {
            try {
                $item->save();
                $time = microtime(true) - $start;
                $itemCount++;
                \Illuminate\Support\Facades\Log::info("Saved $itemCount item in $time seconds.");
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Unable to save an intermediary.");
            }
        }
    }

    public function failed(Exception $exception) {
        foreach ($this->intermediaries as $item) {
            $item->delete();
        }
    }

    private function getFileContent() {
        $file = $this->file->upload;
        /* @var $file UploadedFile */
        $path = $file->full_path;
        $content = Storage::get($path);
        return $content;
    }

    private function makeFromLine($line) {
        $intermediate = new IntermediateRecord();
        $intermediate->header = $this->header;
        $intermediate->line = $line;
        $intermediate->json = "";
        $intermediate->format = $this->file->format;
        $intermediate->contact_csv_id = $this->file->id;
        $intermediate->user_id = $this->file->user_id;
        return $intermediate;
    }

}
