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

    /** @var IntermediateRecord */
    private $intermediateRecord;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(IntermediateRecord $intermediateRecord)
    {
        $this->intermediateRecord = $intermediateRecord;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $csvProcesser = CsvLineProcesserFactory::makeByFormat($this->intermediateRecord->format);
        $header = $this->intermediateRecord->header;
        $line = $this->intermediateRecord->line;
        /* @var $csvProcesser AbstractCsvParser */
        $json = $csvProcesser->getJsonObject($header, $line);
        /* @var $json ContactJsonObj */
        $this->intermediateRecord->json = serialize($json);
        $this->intermediateRecord->save();
    }
}
