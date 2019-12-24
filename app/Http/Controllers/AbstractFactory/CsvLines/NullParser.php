<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\AbstractFactory\CsvLines;
use App\Http\Controllers\AbstractFactory\CsvLines\AbstractCsvParser;
use Exception;

/**
 * Description of NullParser
 *
 * @author jaredclemence
 */
class NullParser extends AbstractCsvParser {
    //put your code here
    public function breakFileIntoLines(string $csvContent): array {
        $this->throwException();
    }

    public function getFieldsFromCsvLine(string $lineText): array {
        $this->throwException();
    }

    public function getFormatObject(): FileFormat {
        $this->throwException();
    }

    public function getHeaderFromFileContnent(string $csvContent): string {
        $this->throwException();
    }

    public function getJsonObject($headerText, $lineText): ContactJsonObj {
        $this->throwException();
    }

    private function throwException() {
        throw new Exception("Parser not found for the indicated file format.");
    }

    public function breakIntoHeaderAndContacts(string $csvContent) {
        $this->throwException();
    }

}
