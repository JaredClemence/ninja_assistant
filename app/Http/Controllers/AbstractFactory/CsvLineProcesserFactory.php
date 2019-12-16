<?php

namespace App\Http\Controllers\AbstractFactory;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AbstractFactory\CsvLines\GoogleCsvParser;
use App\Http\Controllers\AbstractFactory\CsvLines\AbstractCsvParser;
use App\Http\Controllers\AbstractFactory\CsvLines\MagazziOneParser;
use App\Http\Controllers\AbstractFactory\CsvLines\NullParser;

class CsvLineProcesserFactory extends Controller
{
    static private $instantiated = [];
    
    static private function getControllerByClassName( $className ){
        if( isset(self::$instantiated[$className]) == false ){
            self::$instantiated[$className] = new $className();
        }
        return self::$instantiated[$className];
    }
    
    static public function getAvailableFileFormats(){
        return [
            GoogleCsvParser::makeFormat(),
            MagazziOneParser::makeFormat()
        ];
    }
    
    /**
     * Based on the provided format return a different controller for parsing the CSV files.
     * 
     * @param string $format
     * @return AbstractCsvParser
     */
    static public function makeByFormat( string $format = null ) : AbstractCsvParser {
        $formats = self::getAvailableFileFormats();
        $formatClass = array_reduce($formats, function($selectedClass, $formatData) use ($format){
            if( $format == $formatData->format ){
                $selectedClass = $formatData->parser;
            }
            return $selectedClass;
        }, NullParser::class );
        
        $controller = new $formatClass();
        return $controller;
    }
}
