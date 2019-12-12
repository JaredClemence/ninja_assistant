<?php

namespace App\Http\Controllers\AbstractFactory\CsvFiles;

use App\Http\Controllers\AbstractFactory\CsvFiles\AbstractController;
use Illuminate\Support\Facades\Storage;
use App\Clemence\Contact\IntermediateRecord;
use App\Jobs\ConvertIntermideataryToJson;
use App\UploadedFile;
use Exception;

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
        $this->makeJobs();
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
        $lines = $this->breakCsvContentIntoLines($content);
        $this->header = $lines[0];
        $this->getDelegate()->reportHeader( $this->header );
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
            ConvertIntermideataryToJson::dispatch($item);
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
                    $quoteAlt = 0;
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
        $this->getDelegate()->reportLinesCount( count( $lines ) );
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
