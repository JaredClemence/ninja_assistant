<?php

namespace App\Http\Controllers\AbstractFactory\CsvLines;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AbstractFactory\CsvLines\AbstractCsvParser;

class GoogleCsvParser extends AbstractCsvParser
{
    //
    public function getFieldsFromCsvLine(string $lineText): array {
        
    }

    public function breakFileIntoLines(string $csvContent): array {
        
    }

    public function getHeaderFromFileContnent(string $csvContent): string {
        
    }

    public function getJsonObject($headerText, $lineFields): object {
        
    }

}
