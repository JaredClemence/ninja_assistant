<?php

namespace App\Http\Controllers\AbstractFactory\CsvLines;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AbstractFactory\CsvLines\AbstractCsvParser;

class MagazziOneParser extends AbstractCsvParser
{
    public static $label = "Megazzi One";
    public static $format = "megazzi_one";
    
    public function breakFileIntoLines(string $csvContent): array {
        
    }

    public function getFieldsFromCsvLine(string $lineText): array {
        
    }

    public function getHeaderFromFileContnent(string $csvContent): string {
        
    }

    public function getJsonObject($headerText, $lineFields): object {
        
    }
    
    public static function getFormatObject(): FileFormat {
        return $this->makeFileFormat( self::$format, self::$label );
    }

}
