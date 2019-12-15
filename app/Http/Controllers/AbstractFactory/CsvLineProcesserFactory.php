<?php

namespace App\Http\Controllers\AbstractFactory;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AbstractFactory\CsvLines\NullCsvParser;
use App\Http\Controllers\AbstractFactory\CsvLines\GoogleCsvParser;
use App\Http\Controllers\AbstractFactory\CsvLines\AbstractCsvParser;
use App\Http\Controllers\AbstractFactory\CsvLines\MagazziOneParser;

class CsvLineProcesserFactory extends Controller
{
    static private $instantiated = [];
    
    static private function getControllerByClassName( $className ){
        if( isset(self::$instantiated[$className]) == false ){
            self::$instantiated[$className] = new $className();
        }
        return self::$instantiated[$className];
    }
    
    /**
     * Based on the provided format return a different controller for parsing the CSV files.
     * 
     * @param string $format
     * @return AbstractCsvParser
     */
    static public function makeByFormat( string $format = null ) : AbstractCsvParser {
        $controller = null;
        if( $format == 'iphone' ){
            $controller = self::getControllerByClassName(AppleContactsCsvParser::class);
        }
        else if( $format == 'android' ){
            $controller = self::getControllerByClassName(GoogleCsvParser::class);
        }
        else {
            $controller = self::getControllerByClassName(NullCsvParser::class);
        }
        return $controller;
    }
}
