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

/**
 * The abstract controller returns this for new and unprocessed contact 
 * files.
 */
class UnprocessedController extends AbstractController
{
    /** @var ContactCsvFile */
    private $file;
    private $header;
    private $contacts;
    private $intermediaries;
    
    public function process(\App\ContactCsvFile $file) {
        $this->initializeDelegate();
        $this->file = $file;
        $this->parseFile();
        $this->makeIntermediaries();
        $this->saveIntermediaries();
        $this->createNextJob();
        $this->setProcessedDate();
        $this->cleanup();
    }
    
    private function cleanup(){
        unset( $this->file );
        unset( $this->header );
        unset( $this->contacts );
        unset( $this->intermediaries );
    }
    
    private function parseFile() {
        $content = $this->getFileContent();
        /* @var $csvParser AbstractCsvParser */
        $format = $this->file->format;
        $csvParser = CsvLineProcesserFactory::makeByFormat($format);
        list( $header, $lines ) = $csvParser->breakIntoHeaderAndContacts($content);
        $this->header = $header;
        $this->contacts = $lines;
        $this->getDelegate()->reportHeader( $this->header );
    }

    private function makeIntermediaries() {
        $this->intermediaries = new \SplDoublyLinkedList();
        foreach( $this->contacts as $line ){
            $intermediary = $this->makeFromLine( $line );
            $this->intermediaries->push( $intermediary );
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
        foreach($this->intermediaries as $intermediate){
            if( $user == null ){
                $user = \App\User::find( $intermediate->user_id);
            }
            $intermediate = IntermediateRecord::find($intermediate->id);
            if( $intermediate ){
                SingleIntermediaryToJson::dispatch($intermediate);
            }
        }
        if( $user != null ){
            Mail::queue(new VerifyContactDetailsNotice($user));
        }
    }

    private function saveIntermediaries() {
        $count = count($this->intermediaries);
        $itemCount = 0;
        $start = microtime(true);
        \Illuminate\Support\Facades\Log::info( "Saving $count intermediary files.");
        foreach( $this->intermediaries as $item ){
            $item->save();
            $time = microtime(true) - $start;
            $itemCount++;
            \Illuminate\Support\Facades\Log::info("Saved $itemCount item in $time seconds.");
        }
    }
    
    public function failed(Exception $exception){
        foreach( $this->intermediaries as $item ){
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
