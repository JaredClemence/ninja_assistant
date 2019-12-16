<?php

namespace App\Http\Controllers\AbstractFactory\CsvLines;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AbstractFactory\CsvLines\AbstractCsvParser;
use App\Http\Controllers\AbstractFactory\CsvLines\ContactJsonObj;
use App\Http\Controllers\AbstractFactory\CsvLines\PhoneJsonObj;

class MagazziOneParser extends AbstractCsvParser
{
    public static $label = "Megazzi One";
    public static $format = "megazzi_one";
    
    public static $header = "Last Name,First Name,Street, City, State, Zip, Source, Note, Email, Phone";
    
    public static function makeFormat(){
        $format = parent::makeFileFormat(self::$format, self::$label, MagazziOneParser::class);
        return $format;
    }
    
    public function breakFileIntoLines(string $csvContent): array {
        return split("\n", $csvContent);;
    }

    public function getFieldsFromCsvLine(string $lineText): array {
        $map = str_getcsv($lineText);
        $map = array_map( function($text){ return trim($text); }, $map );
        return $map;
    }

    public function getHeaderFromFileContnent(string $csvContent): string {
        return self::$header;
    }

    public function getJsonObject($headerText, $lineText): ContactJsonObj {
        $map = $this->makeMap( $headerText, $lineText );
        $contact = new ContactJsonObj();
        $contact->name = $this->castString( trim( $map['First Name'] . ' ' . $map['Last Name']) );
        $contact->address = $this->castString( $map['Street'] . '; ' . $map['City'] . ', ' . $map['State'] . ' ' . $map['Zip'] );
        $contact->notes = $this->castString( $map['Source'] . ' ' . $map['Note'] );
        $contact->phones = [
            $this->makePhone($map['Phone'])
        ];
        $contact->email = $this->castString( $map['Email'] );
        return $contact;
    }
    
    public function getFormatObject(): FileFormat {
        return $this->makeFileFormat( self::$format, self::$label );
    }

    private function makeMap($headerText, $lineText) {
        $headers = $this->getFieldsFromCsvLine($headerText);
        $lineText = $this->getFieldsFromCsvLine($lineText);
        $map = [];
        while( count($headers) ){
            $header = array_shift($headers);
            $data = array_shift($lineText);
            $map[$header]=$data;
        }
        return $map;
    }

    private function castString($mixed) {
        return trim($mixed);
    }

    private function makePhone($number) {
        $phone = new PhoneJsonObj();
        $phone->number = $this->castString($number);
        $phone->type = 'Mobile';
        return $phone;
    }

}
