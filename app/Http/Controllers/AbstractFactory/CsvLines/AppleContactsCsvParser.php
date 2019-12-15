<?php

namespace App\Http\Controllers\AbstractFactory\CsvLines;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AbstractFactory\CsvLines\AbstractCsvParser;

class AppleContactsCsvParser extends AbstractCsvParser
{
    static public $format = "apple_contacts_csv";
    static public $label = "Numbers CSV";
    //
    public function getFieldsFromCsvLine(string $lineText): array {
        
    }

    public function getHeaderFromFileContnent(string $csvContent): string {
        
    }

    public function getJsonObject($headerText, $lineFields): object {
        
    }

    public function breakFileIntoLines(string $csvContent): array {
        
    }

    public static function getFormatObject(): FileFormat {
        return $this->makeFileFormat( self::$format, self::$label );
    }

}
