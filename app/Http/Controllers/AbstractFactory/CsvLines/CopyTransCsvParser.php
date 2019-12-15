<?php

namespace App\Http\Controllers\AbstractFactory\CsvLines;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AbstractFactory\CsvLines\AbstractCsvParser;

class CopyTransCsvParser extends AbstractCsvParser
{
    static public $format = "copy_trans_csv";
    static public $label = "Copy Trans CSV";

    public static function getFormatObject(): FileFormat {
        return static::makeFileFormat( self::$format, self::$label );
    }

    public function breakFileIntoLines(string $csvContent): array {
        
    }

    public function getFieldsFromCsvLine(string $lineText): array {
        
    }

    public function getHeaderFromFileContnent(string $csvContent): string {
        
    }

    public function getJsonObject($headerText, $lineFields): object {
        
    }

}
