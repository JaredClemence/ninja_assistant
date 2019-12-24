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
    abstract public function getJsonObject( $headerText, $lineText ):ContactJsonObj;
    abstract public function getFormatObject():FileFormat;
    abstract public function breakIntoHeaderAndContacts(string $csvContent);
    
    protected static function makeFileFormat($format_text, $label, $parser) : FileFormat {
        $format = new FileFormat();
        $format->fill([
                    'format'=>$format_text,
                    'label'=>$label,
                    'parser'=>$parser
                ]);
        return $format;
    }
}
