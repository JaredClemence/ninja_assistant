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
    public static function make( $contactCsvFile ): AbstractController {
        if(is_null($contactCsvFile)){
            return new NullController();
        }
        $uploadFile = $contactCsvFile->upload;
        return self::makeFromUploadFile( $uploadFile );
    }

    private static function makeFromUploadFile($uploadFile) : AbstractController {
        $controller = null;
        /* @var $uploadFile UploadedFile */
        if(is_null($uploadFile)){
            $controller =  new NullController();
        }else if( $uploadFile->archived ){
            $controller =  new ArchivedController();
        }else if( $uploadFile->processed ){
            $controller =  new ProcessedController();
        }else{
            $controller = new UnprocessedController();
        }
        return $controller;
    }

}
