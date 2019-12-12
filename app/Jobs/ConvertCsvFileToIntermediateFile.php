<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Clemence\Contact\IntermediateRecord;
use App\ContactCsvFile;
use App\UploadedFile;
use Exception;

class ConvertCsvFileToIntermediateFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var ContactCsvFile */
    private $file;
    private $header;
    private $contacts;
    private $intermediaries;
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
        if( $this->delegate == null ){
            $this->fakeDelegate();
        }
    }
    
    private function fakeDelegate(){
        $this->delegate = new \stdClass();
        $this->delegate->reportLinesCount = function($a){};
        $this->delegate->reportHeader = function($a){};
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->parseFile();
        $this->makeIntermediaries();
        $this->saveIntermediaries();
        $this->makeJobs();
        $this->setProcessedDate();
    }

    private function parseFile() {
        $content = $this->getFileContent();
        $lines = $this->breakCsvContentIntoLines($content);
        $this->header = $lines[0];
        $this->delegate->reportHeader( $this->header );
        $this->contacts = \array_slice( $lines, 1 );
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

    private function makeJobs() {
        foreach( $this->intermediaries as $item ){
            //dispatch job here.
        }
    }

    private function saveIntermediaries() {
        foreach( $this->intermediaries as $item ){
            $item->save();
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

    private function breakCsvContentIntoLines($content) {
        $lines = [];
        $line = "";
        $quoteCount = 0;
        $quoteAlt = 0;
        for($i=0; $i<strlen( $content); $i++ ){
            $char = $content[$i];
            if( $char == '"'){
                if( $quoteAlt == 1 ){
                    $line .= '"';
                }else if( $quoteCount == 0 ){
                    $quoteCount = 1;
                }else{
                    $quoteCount = 0;
                }
            }
            else if( $char == "\n" ){
                if( $quoteCount == 0 ){
                    $lines[] = $line;
                    $line = "";
                }
            }
            else {
                $line .= $char;
            }
        }
        $lines[] = $line;
        $this->delegate->reportLinesCount( count( $lines ) );
        return $lines;
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
