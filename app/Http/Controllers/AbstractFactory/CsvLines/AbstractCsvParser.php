<?php

namespace App\Http\Controllers\AbstractFactory\CsvLines;

use App\Http\Controllers\Controller;

abstract class AbstractCsvParser extends Controller
{
    abstract public function breakFileIntoLines(string $csvContent):array;
    abstract public function getHeaderFromFileContnent(string $csvContent):string;
    abstract public function getFieldsFromCsvLine(string $lineText):array;
    abstract public function getJsonObject( $headerText, $lineFields ):object;
}
