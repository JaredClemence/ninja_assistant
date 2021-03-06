<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\ContactCsvFile;
use Illuminate\Support\Facades\Log;

class ConvertCsvFileToIntermediateFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $timeout = 500;
    
    /** @var ContactCsvFile */
    private $file;
    private $delegate;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ContactCsvFile $file, $delegate = null)
    {
        $this->file = $file;
        $this->delegate = $delegate;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->file->process($this->delegate);
        Log::info("Convert CSV file finished.");
        return;
    }
    
    
    public function failed(\Exception $exception){
        Log::info("Job failed: ConvertCsvFileToIntermediateFile");
        Log::error($exception->getMessage());
        Log::debug("Failure occured in file: " . $exception->getFile());
        Log::debug("Failure occured on line: " . $exception->getLine());
        Log::debug("Debug backtace: " . $exception->getTraceAsString());
    }

}
