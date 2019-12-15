<?php

namespace App\Http\Controllers\AbstractFactory\CsvLines;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AbstractFactory\CsvLines\FileFormat;
use App\Http\Controllers\AbstractFactory\CsvLines\ContactJsonObj;

abstract class AbstractCsvParser extends Controller
{
    abstract public function breakFileIntoLines(string $csvContent):array;
    abstract public function getHeaderFromFileContnent(string $csvContent):string;
    abstract public function getFieldsFromCsvLine(string $lineText):array;
    abstract public function getJsonObject( $headerText, $lineFields ):ContactJsonObj;
    abstract public function getFormatObject():FileFormat;
    
    protected static function makeFileFormat($format, $label) : FileFormat {
        $format = new FileFormat();
        $format->fill([
                    'format'=>$format,
                    'label'=>$label
                ]);
        return $format;
    }
}
