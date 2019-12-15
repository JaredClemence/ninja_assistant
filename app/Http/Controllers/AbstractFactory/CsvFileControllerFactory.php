<?php

namespace App\Http\Controllers\AbstractFactory;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AbstractFactory\CsvFiles\ArchivedController;
use App\Http\Controllers\AbstractFactory\CsvFiles\ProcessedController;
use App\Http\Controllers\AbstractFactory\CsvFiles\UnprocessedController;
use App\Http\Controllers\AbstractFactory\CsvFiles\NullController;
use App\Http\Controllers\AbstractFactory\CsvFiles\AbstractController;

use App\ContactCsvFile;
use App\UploadedFile;

class CsvFileControllerFactory extends Controller
{
    static private $instantiated = [];
    
    public static function make( $contactCsvFile ): AbstractController {
        if(is_null($contactCsvFile)){
            return new NullController();
        }
        $uploadFile = $contactCsvFile->upload;
        return self::makeFromUploadFile( $uploadFile );
    }
    
    public static function getAvailableFileFormats(){
        return [
            
        ];
    }

    private static function makeFromUploadFile($uploadFile) : AbstractController {
        $controller = null;
        /* @var $uploadFile UploadedFile */
        if(is_null($uploadFile)){
            $controller =  self::getControllerByClass( NullController::class );
        }else if( $uploadFile->archived ){
            $controller =  self::getControllerByClass( ArchivedController::class );
        }else if( $uploadFile->processed ){
            $controller =  self::getControllerByClass( ProcessedController::class );
        }else{
            $controller = self::getControllerByClass( UnprocessedController::class );
        }
        return $controller;
    }
    
    private static function getControllerByClass( $className ){
        if( isset( self::$instantiated[$className] )==false){
            self::$instantiated[$className] = new $className();
        }
        return self::$instantiated[$className];
    }

}
