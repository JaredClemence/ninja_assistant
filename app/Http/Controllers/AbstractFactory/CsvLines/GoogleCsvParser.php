<?php

namespace App\Http\Controllers\AbstractFactory\CsvLines;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AbstractFactory\CsvLines\AbstractCsvParser;
use App\Http\Controllers\AbstractFactory\CsvLines\ContactJsonObj;
use App\Http\Controllers\AbstractFactory\CsvLines\PhoneJsonObj;

class GoogleCsvParser extends AbstractCsvParser {

    static public $format = "google_csv";
    static public $label = "Google CSV";

    public static function makeFormat() {
        return parent::makeFileFormat(self::$format, self::$label, GoogleCsvParser::class);
    }

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

    public function breakIntoHeaderAndContacts(string $csvContent) {
        $lines = $this->breakFileIntoLines($csvContent);
        $header = $lines[0];
        $lines = array_slice($lines, 1);
        return [$header, $lines];
    }

    public function getHeaderFromFileContnent(string $csvContent): string {
        list( $header, $unused ) = $this->breakIntoHeaderAndContacts($csvContent);
        return $header;
    }

    public function getJsonObject($headerText, $lineText): ContactJsonObj {
        $headers = $this->getFieldsFromCsvLine($headerText);
        $data = $this->getFieldsFromCsvLine($lineText);
        $map = $this->makeMap($headers, $data);
        $obj = new ContactJsonObj();
        $obj->name = $this->getName($map);
        $obj->address = $this->getAddress($map);
        $obj->notes = $this->getNotes($map);
        $obj->phones = $this->getPhones($map);
        $obj->birthday = $this->getBirthday($map);
        $obj->email = $this->getEmail($map);
        return $obj;
    }

    private function getName($map) {
        $fname = isset($map['Given Name']) ? $map['Given Name'] : '';
        $lname = isset($map['Family Name']) ? $map['Family Name'] : '';

        $name = '';
        if (isset($map['Name']) && $map['Name'] != '') {
            $name = $map['Name'];
        } else if ($fname != '' || $lname != '') {
            $name = trim("$fname $lname");
        }
        return $name;
    }

    private function getAddress($map) {
        $address = null;
        if (isset($map['Address 1 - Street'])&&trim($map['Address 1 - Street'])!=='') {
            $address = $this->buildAddressFromParts(1, $map);
        } else if (isset($map['Address 1 - Formatted'])) {
            $address = $map['Address 1 - Formatted'];
        } else {
            
        }
        return $address;
    }

    private function getNotes($map) {
        $notes = isset($map['Notes']) ? $map['Notes'] : '';
        return $notes;
    }

    private function getPhones($map) {
        $phones = [];
        for ($i = 1; $i < 4; $i++) {
            $tempPhones = $this->getPhoneByNumber($map, $i);
            if (count($tempPhones)>0) {
                foreach($tempPhones as $phone){
                    $phones[] = $phone;
                }
            }
        }
        return $phones;
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
            if ($pos !== false) {
                $loc = $pos + 1;
                $leftStr = substr($line, 0, $pos);
                $rightStr = substr($line, $pos + 1);
                $line = $leftStr . $rightStr;
            }
        } while ($pos !== false && $loc < strlen($line));
        return $line;
    }

    private function getBirthday($map) {
        $date = isset($map['Birthday']) ? $map['Birthday'] : '';
        return $date;
    }

    private function buildPhoneJson($type, $number) {
        $phone = new PhoneJsonObj();
        $phone->type = $type;
        $phone->number = $number;
        return $phone;
    }

    private function getPhoneByNumber($map, $i) {
        $typeIndex = "Phone $i - Type";
        $valueIndex = "Phone $i - Value";
        $type = isset($map[$typeIndex]) ? $map[$typeIndex] : null;
        $value = isset($map[$valueIndex]) ? $map[$valueIndex] : null;
        $phones = [];
        if ($this->containsTripplet($value)) {
            $values = $this->breakTripplet($value);
        } else {
            $values = [$value];
        }
        foreach($values as $value ){
            if ($type || $value) {
                $phone = $this->buildPhoneJson($type, $value);
                $phones[] = $phone;
            }
        }
        return $phones;
    }

    protected function makeMap($headers, $data) {
        $array = [];
        do {
            $header = array_shift($headers);
            $datum = array_shift($data);
            if ($header) {
                $array[$header] = $this->cleanData($datum);
            } else {
                $array[] = $datum;
            }
        } while (count($headers) || count($data));
        return $array;
    }

    private function getEmail($map) {
        $emails = [];
        for ($i = 1; $i < 4; $i++) {
            $key = "E-mail $i - Value";
            $email = $map[$key];
            if ($email) {
                $emails[] = $email;
            }
        }
        return \join(', ', $emails);
    }

    private function buildAddressFromParts($num, $map) {
        $street = $this->removeTripplet($map["Address $num - Street"]);
        $city = $this->removeTripplet($map["Address $num - City"]);
        $po_box = $this->removeTripplet($map["Address $num - PO Box"]);
        $region = $this->removeTripplet($map["Address $num - Region"]);
        $postal_code = $this->removeTripplet($map["Address $num - Postal Code"]);
        $country = $this->removeTripplet($map["Address $num - Country"]);
        $addressParts = [];
        if (trim($street) !== '') {
            $addressParts[] = $street;
        }
        if (trim($po_box) !== '') {
            $addressParts[] = "PO Box $po_box";
        }
        $addressParts[] = "$city, $region $postal_code";
        if (trim($country) !== '') {
            $addressParts[] = $country;
        }
        $address = implode("; ", $addressParts);
        return $address;
    }

    private function removeTripplet($string) {
        $parts = $this->breakTripplet($string);
        $cleanParts = array_filter($parts, function($line) {
            return trim($line) != '';
        });

        if (count($cleanParts) == 0)
            return '';
        if (count($cleanParts) == 1)
            return array_shift($cleanParts);
        if (count($cleanParts) == 2 && $cleanParts[0] == $cleanParts[1])
            return $cleanParts[0];
        return $string;
    }

    private function cleanData($string) {
        return $this->removeHTCData($this->removeTripplet($string));
    }

    private function removeHTCData($string) {
        if (preg_match('#<HTCData>.*</HTCData>#', $string, $match)) {
            $xml = $match[0];
            $string = str_replace($xml, "", $string);
        }
        return $string;
    }

    private function breakTripplet($string) {
        $token = " ::: ";
        $parts = explode($token, $string);
        return $parts;
    }

    private function containsTripplet($string) {
        $parts = $this->breakTripplet($string);
        return count($parts) > 1;
    }

}
