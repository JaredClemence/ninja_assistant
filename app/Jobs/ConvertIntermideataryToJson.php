<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Clemence\Contact\IntermediateRecord;
use App\Http\Controllers\AbstractFactory\CsvLineProcesserFactory;
use App\Http\Controllers\AbstractFactory\CsvLines\AbstractCsvParser;
use App\Http\Controllers\AbstractFactory\CsvLines\ContactJsonObj;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyContactDetailsNotice;
use Illuminate\Support\Facades\Log;
use App\User;

class ConvertIntermideataryToJson implements ShouldQueue {

    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    public $timeout = 500;
    private $intermediateRecords;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($intermediateRecords) {
        $array = $this->convertToArray( $intermediateRecords );
        list($is_array, $is_record) = $this->testRecordSet($array);
        Log::info("Creating a new job to convert the intermediary records to JSON.");
        Log::info("The incoming data " . ($is_array ? "is" : "is not") . " an array.");
        Log::info("Every record of the incoming data " . ($is_record ? "is" : "is not") . " an IntermediateRecord object.");
        if (!$is_record) {
            foreach ($intermediateRecords as $i => $record) {
                $type = "unknown";
                if (is_object($record)) {
                    $type = get_class($record);
                } else if (is_string($record) && !is_numeric($record)) {
                    $type = "non-numeric string";
                } else if (is_numeric($record)) {
                    $type = "number";
                }
                Log::info("The record in array possition $i is a $type.");
            }
        }
        $this->intermediateRecords = $array;
    }

    public function __wakeup() {
        list($is_array, $is_record) = $this->testRecordSet($this->intermediateRecords);
        Log::info("Waking up job to convert intermediate records to JSON.");
        Log::info("The stored data " . ($is_array ? "is" : "is not") . " an array.");
        Log::info("Every record of the stored data " . ($is_record ? "is" : "is not") . " an IntermediateRecord object.");
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        try {
            Log::info("ConvertIntermideataryToJson::handle() method called.");
            $start = microtime(true);
            $csvProcesser = null;
            $count = count($this->intermediateRecords);
            $saved = 0;
            $user = null;
            Log::info("Starting work with $count records to process and save.");
            foreach ($this->intermediateRecords as $record) {
                if ($csvProcesser === null)
                    $csvProcesser = CsvLineProcesserFactory::makeByFormat($record->format);
                $this->handleRecord($csvProcesser, $record);
                $record->save();
                $saved++;
                if ($user === null) {
                    $id = $record->user_id;
                    Log::info("User id extracted from record is $id.");
                    $user = User::find($record->user_id);
                    if ($user === null) {
                        Log::info("User not found with id $id.");
                    } else {
                        $name = $user->name;
                        Log::info("User loaded with name: $name.");
                    }
                }
                $timeSpent = microtime(true) - $start;
                Log::error("Saved $saved out of $count in $timeSpent seconds.");
                $count++;
            }
            Log::info("Sending email notification.");
            $this->sendEmailNotification($user);
            Log::info("ConvertIntermideataryToJson::handle() method completed successfully.");
        } catch (Exception $e) {
            $exceptionMessage = $e->getMessage();
            $file = $e->getFile();
            $line = $e->getLine();
            Log::error($exceptionMessage . " thrown from $file at $line.");
        }
    }

    private function handleRecord(&$csvProcesser, &$record) {
        $header = $record->header;
        $line = $record->line;
        /* @var $csvProcesser AbstractCsvParser */
        $json = $csvProcesser->getJsonObject($header, $line);
        /* @var $json ContactJsonObj */
        $record->json = serialize($json);
    }

    private function sendEmailNotification($user) {
        if (is_a($user, User::class)) {
            Mail::send(new VerifyContactDetailsNotice($user));
        }
    }

    private function testRecordSet($intermediateRecords) {
        $is_array = is_array($intermediateRecords);
        $is_record = array_reduce($intermediateRecords, function($carryover, $item) {
            if (is_a($item, IntermediateRecord::class) == false) {
                $carryover = false;
            }
            return $carryover;
        }, true);
        return [$is_array, $is_record];
    }

    private function convertToArray($intermediateRecords) {
        $array = $intermediateRecords;
        if( is_array($intermediateRecords) == false ){
            if(method_exists($intermediateRecords, "all")){
                $array = $intermediateRecords->all();
            }else if(is_a($intermediateRecords, \Traversable::class)){
                $array = [];
                foreach( $intermediateRecords as $item ){
                    $array[] = $item;
                }
            }
        }
        return $array;
    }

}
