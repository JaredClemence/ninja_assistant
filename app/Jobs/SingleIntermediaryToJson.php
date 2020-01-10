<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Clemence\Contact\IntermediateRecord;

class SingleIntermediaryToJson implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $record;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(IntermediateRecord $record)
    {
        $this->record = $record;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $record = $this->record;
        $csvProcesser =  CsvLineProcesserFactory::makeByFormat($record->format);
        $this->handleRecord($csvProcesser, $record);
        $record->save();
    }
    
    private function handleRecord(&$csvProcesser, &$record) {
        $header = $record->header;
        $line = $record->line;
        /* @var $csvProcesser AbstractCsvParser */
        $json = $csvProcesser->getJsonObject($header, $line);
        /* @var $json ContactJsonObj */
        $record->json = serialize($json);
    }
}
