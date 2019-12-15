<?php

namespace App\Http\Controllers\AbstractFactory\CsvLines;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AbstractFactory\CsvLines\AbstractCsvParser;
use App\Http\Controllers\AbstractFactory\CsvLines\ContactJsonObj;

class GoogleCsvParser extends AbstractCsvParser {

    static public $format = "google_csv";
    static public $label = "Google CSV";

    public function getFieldsFromCsvLine(string $lineText): array {
        $lines = $this->breakIntoColumnData($lineText);
        return $this->removeQuotes($lines);
    }

    public function breakFileIntoLines(string $content): array {
        $lines = [];
        $line = "";
        $quoteCount = 0;
        for ($i = 0; $i < strlen($content); $i++) {
            $char = $content[$i];
            if ($char == '"') {
                if ($quoteCount == 0) {
                    $quoteCount = 1;
                } else {
                    $quoteCount = 0;
                }
            } else if ($char == "\n") {
                if ($quoteCount == 0) {
                    $lines[] = $line;
                    $line = "";
                } else {
                    $line .= $char;
                }
            } else {
                $line .= $char;
            }
        }
        $lines[] = $line;
        return $lines;
    }

    public function getHeaderFromFileContnent(string $csvContent): string {
        $lines = $this->breakFileIntoLines($csvContent);
        return $lines[0];
    }

    public function getJsonObject($headerText, $lineFields): ContactJsonObj {
        $obj = new ContactJsonObj();
        return $obj;
    }

    public function getFormatObject(): FileFormat {
        return self::makeFileFormat(self::$format, self::$label);
    }

    private function breakIntoColumnData($lineText) {
        $parts = [];
        $part = "";
        $quoteCount = 0;
        for ($i = 0; $i < strlen($lineText); $i++) {
            $char = $lineText[$i];
            if ($char == ",") {
                if ($quoteCount == 1) {
                    $part .= $char;
                } else {
                    $parts[] = $part;
                    $part = "";
                }
            } else if ($char == '"') {
                if ($quoteCount == 1) {
                    $quoteCount = 0;
                } else {
                    $quoteCount = 1;
                }
                $part .= $char;
            } else {
                $part .= $char;
            }
        }
        return $parts;
    }

    private function removeQuotes($lines) {
        foreach ($lines as &$line) {
            $line = $this->removeQuotesFromSingleSegement($line);
        }
        return $lines;
    }

    private function removeQuotesFromSingleSegement($line) {
        $loc = 0;
        do {
            $pos = strpos($line, '"', $loc);
            if ($pos!==false) {
                $loc = $pos + 1;
                $leftStr = substr($line, 0, $pos);
                $rightStr = substr($line, $pos + 1);
                $line = $leftStr . $rightStr;
            }
        } while ($pos !== false && $loc < strlen($line));
        return $line;
    }

}
