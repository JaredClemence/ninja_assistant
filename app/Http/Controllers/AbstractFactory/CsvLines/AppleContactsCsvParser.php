<?php

namespace App\Http\Controllers\AbstractFactory\CsvLines;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AbstractFactory\CsvLines\AbstractCsvParser;

class AppleContactsCsvParser extends AbstractCsvParser
{
    //
    public function getFieldsFromCsvLine(string $lineText): array {
        
    }

    public function getHeaderFromFileContnent(string $csvContent): string {
        
    }

    public function getJsonObject($headerText, $lineFields): object {
        
    }

    public function breakFileIntoLines(string $csvContent): array {
        
    }

}
