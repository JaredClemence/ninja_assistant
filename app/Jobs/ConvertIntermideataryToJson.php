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

class ConvertIntermideataryToJson implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $timeout = 500;

    private $intermediateRecords;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($intermediateRecords)
    {
        $this->intermediateRecords = collect($intermediateRecords);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("Logging activity in ConvertIntermideataryToJson job.");
        $start = microtime(true);
        $csvProcesser = null;
        $count = count($this->intermediateRecords);
        $saved = 0;
        $user = null;
        \Illuminate\Support\Facades\Log::error("Starting work with $count records to process and save.");
        foreach($this->intermediateRecords as $record ){
            if( $csvProcesser === null ) $csvProcesser = CsvLineProcesserFactory::makeByFormat($record->format);
            $this->handleRecord($csvProcesser, $record);
            $record->save();
            if($user===null){ 
                $id = $record->user_id;
                Log::info("User id extracted from record is $id.");
                $user = User::find($record->user_id);
                if( $user === null ){
                    Log::info("User not found with id $id.");
                }else{
                    $name = $user->name;
                    Log::info("User loaded with name: $name.");
                }
            }
            $timeSpent = microtime(true) - $start;
            \Illuminate\Support\Facades\Log::error("Saved $saved out of $count in $timeSpent seconds.");
            $count++;
        }
        $this->sendEmailNotification($user);
        Log::info("Completing log of activity in ConvertIntermideataryToJson job.");
        
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
        if( is_a($user, User::class)){
            Mail::send(new VerifyContactDetailsNotice($user));
        }
    }

}
