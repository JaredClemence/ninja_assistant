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

class ConvertIntermideataryToJson implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $csvProcesser = null;
        foreach($this->intermediateRecords as $record ){
            if( $csvProcesser === null ) $csvProcesser = CsvLineProcesserFactory::makeByFormat($this->intermediateRecord->format);
            $this->handleRecord($csvProcesser, $record);
        }
        $this->intermediateRecords->save();
        $this->sendEmailNotification();
        
    }

    private function handleRecord(&$csvProcesser, &$record) {
        $header = $record->header;
        $line = $record->line;
        /* @var $csvProcesser AbstractCsvParser */
        $json = $csvProcesser->getJsonObject($header, $line);
        /* @var $json ContactJsonObj */
        $record->json = serialize($json);
    }

    private function sendEmailNotification() {
        
    }

}
