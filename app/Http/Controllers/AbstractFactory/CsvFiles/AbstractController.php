<?php

namespace App\Http\Controllers\AbstractFactory\CsvFiles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ContactCsvFile;
use App\UploadedFile;
use Exception;

abstract class AbstractController extends Controller
{
    private $delegate;
    
    private function fakeDelegate(){
        $this->delegate = new \stdClass();
        $this->delegate->reportLinesCount = function($a){};
        $this->delegate->reportHeader = function($a){};
    }
    
    protected function initializeDelegate() {
        if($this->delegate==null){
            $this->fakeDelegate();
        }
    }
    
    public function setDelegate( $delegate ){
        $this->delegate = $delegate;
    }
    
    public function getDelegate(){ return $this->delegate; }
    
    public function archive(ContactCsvFile $contact){
        $identifier = $this->getFileIdentifier($contact);
        throw new Exception("$identifier cannot be archived in the present state.");
    }  
    
    public function delete(ContactCsvFile $file){
        $identifier = $this->getFileIdentifier($contact);
        throw new Exception("$identifier cannot be deleted in the present state.");
    }
    
    public function process(ContactCsvFile $file){
        $identifier = $this->getFileIdentifier($contact);
        throw new Exception("$identifier cannot be processed in the present state.");
    }
    
    private function getFileIdentifier( ContactCsvFile $contact ){
        $file = $this->getFileReference($contact);
        /* @var $file UploadedFile */
        $name = $file->name;
        $id = $file->id;
        return "Contact CSV File ($id, $name)";
    }
    /**
     * 
     * @param ContactCsvFile $contact
     * @return App\UploadedFile
     */
    private function getFileReference( ContactCsvFile $contact ){
        return $contact->upload;
    }
}
