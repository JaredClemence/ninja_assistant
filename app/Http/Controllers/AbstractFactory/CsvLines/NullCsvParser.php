<?php

namespace App\Http\Controllers\AbstractFactory\CsvLines;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AbstractFactory\CsvLines\AbstractCsvParser;

class NullCsvParser extends AbstractCsvParser
{
    public function getFieldsFromCsvLine(string $lineText): array {
        $this->throwException();
    }

    public function getHeaderFromFileContnent(string $csvContent): string {
        $this->throwException();
    }

    public function getJsonObject($headerText, $lineFields): object {
        $this->throwException();
    }

    private function throwException() {
        throw new Exception("Unrecognized file format. NullCsvParser is being used.");
    }

    public function breakFileIntoLines(string $csvContent): array {
        
    }

}
