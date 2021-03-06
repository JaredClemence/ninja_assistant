<?php

namespace Tests\Unit\Factories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Controllers\AbstractFactory\CsvLines\GoogleCsvParser;

class GoogleCsvLineParserTest extends TestCase
{
    static public $header = "Name,Given Name,Additional Name,Family Name,Yomi Name,Given Name Yomi,Additional Name Yomi,Family Name Yomi,Name Prefix,Name Suffix,Initials,Nickname,Short Name,Maiden Name,Birthday,Gender,Location,Billing Information,Directory Server,Mileage,Occupation,Hobby,Sensitivity,Priority,Subject,Notes,Language,Photo,Group Membership,E-mail 1 - Type,E-mail 1 - Value,E-mail 2 - Type,E-mail 2 - Value,E-mail 3 - Type,E-mail 3 - Value,IM 1 - Type,IM 1 - Service,IM 1 - Value,Phone 1 - Type,Phone 1 - Value,Phone 2 - Type,Phone 2 - Value,Phone 3 - Type,Phone 3 - Value,Address 1 - Type,Address 1 - Formatted,Address 1 - Street,Address 1 - City,Address 1 - PO Box,Address 1 - Region,Address 1 - Postal Code,Address 1 - Country,Address 1 - Extended Address,Address 2 - Type,Address 2 - Formatted,Address 2 - Street,Address 2 - City,Address 2 - PO Box,Address 2 - Region,Address 2 - Postal Code,Address 2 - Country,Address 2 - Extended Address,Organization 1 - Type,Organization 1 - Name,Organization 1 - Yomi Name,Organization 1 - Title,Organization 1 - Department,Organization 1 - Symbol,Organization 1 - Location,Organization 1 - Job Description,Relation 1 - Type,Relation 1 - Value,Website 1 - Type,Website 1 - Value";
    static public $testDataOne = <<<TESTDATAONE
James Chon,James,,Chon,,,,,,,,,,,,,,,,,,,,,,Sold Tarah the Highlander lease.,,,* myContacts,* Home,chonjames88@hotmail.com,,,,,,,,Mobile,(661) 448-9594,Work,(661) 615-1100,,,,,,,,,,,,,,,,,,,,,unknown,North Bakersfield Toyota,,,,,,,,,,
James P Stahl,James P,,Stahl,,,,,,,,,,,,,,,,,,,,,,"Initiated at Princeton 38 on 26APR2010

Mason",,https://lh6.googleusercontent.com/-eP0NVoxP728/WEBWGQKUWKI/AAAAAAAAAAA/J5dvQ29W3QsEjqPi4N5ejFR84B4Sni4IACOQCEAE/photo.jpg,* myContacts,* Work,james.p.stahl@gmail.com,,,,,,,,Mobile,908-295-4848,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
"Adrianna ""Gaby"" Martinez","Adrianna ""Gaby""",,Martinez,,,,,,,,,,,,,,,,,,,,,,,,,LawSchool ::: * myContacts,* Other,amartinez@kerncountylaw.org,,,,,,,,Mobile,+1 661-364-3180,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
James Setaro,James,,Setaro,,,,,,,,,,,,,,,,,,,,,,,,,* myContacts,* ,james@haveitallhomes.com,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
TESTDATAONE;
    static public $lineBreakLine = <<<SINGLELINE
James P Stahl,James P,,Stahl,,,,,,,,,,,,,,,,,,,,,,"Initiated at Princeton 38 on 26APR2010

Mason",,https://lh6.googleusercontent.com/-eP0NVoxP728/WEBWGQKUWKI/AAAAAAAAAAA/J5dvQ29W3QsEjqPi4N5ejFR84B4Sni4IACOQCEAE/photo.jpg,* myContacts,* Work,james.p.stahl@gmail.com,,,,,,,,Mobile,908-295-4848,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
SINGLELINE;
    
    /**
     * Test breaking three lines that include new lines within quotes.
     *
     * @return void
     * @group parser
     */
    public function testParserReadsLinesWithInternalLineBreaks()
    {
        $fileData = self::$header . PHP_EOL . self::$testDataOne;
        $parser = new GoogleCsvParser();
        $lines = $parser->breakFileIntoLines($fileData);
        $this->assertEquals( 5, count($lines), "The parser did not identify the internal line break.");
    }
    
    /**
     * Test breaking three lines that include quotes within quotes.
     *
     * @return void
     * @group parser
     */
    public function testParserReadsLinesWithInternalQuotes()
    {
        $fileData = self::$header . PHP_EOL . self::$testDataOne;
        $parser = new GoogleCsvParser();
        $lines = $parser->breakFileIntoLines($fileData);
        $this->assertEquals( 5, count($lines), "The parser did not identify the internal line break.");
    }
    
    /**
     * Test breaking three lines that include quotes within quotes.
     *
     * @return void
     * @group parser
     */
    public function testParserBreaksLineWithInternalBreaksIntoParts(){
        $parser = new GoogleCsvParser();
        $parts = $parser->getFieldsFromCsvLine(self::$lineBreakLine);
        $this->assertEquals("James P Stahl", $parts[0]);
        $this->assertEquals("James P", $parts[1]);
        $this->assertEquals("", $parts[2]);
        $this->assertEquals("Stahl", $parts[3]);
        $crazyBit = "Initiated at Princeton 38 on 26APR2010\n\nMason";
        $this->assertEquals($crazyBit, $parts[25]);
    }
    
    /**
     * Test breaking three lines that include quotes within quotes.
     *
     * @return void
     * @group parser
     */
    public function testParserBreaksLineWithInternalQuotesIntoParts(){
        $parser = new GoogleCsvParser();
        $line = <<<LINE
"Adrianna ""Gaby"" Martinez","Adrianna ""Gaby""",,Martinez,,,,,,,,,,,,,,,,,,,,,,,,,LawSchool ::: * myContacts,* Other,amartinez@kerncountylaw.org,,,,,,,,Mobile,+1 661-364-3180,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
LINE;
        $columnData = $parser->getFieldsFromCsvLine($line);
        $expected = "Adrianna \"Gaby\" Martinez";
        $this->assertEquals( $expected, $columnData[0] );
    }
    
    /**
     * @group parser
     * @dataProvider jsonDataProvider
     */
    public function testJsonObjects($line, $name){
        $parser = new GoogleCsvParser();
        $obj = $parser->getJsonObject(self::$header, $line);
        $this->assertEquals($name, $obj->name, "The name does not match expected values.");
    }
    
    public function jsonDataProvider(){
        return [
            //'Gaby (Given Name)'=>['"Adrianna ""Gaby"" Martinez","Adrianna ""Gaby""",,Martinez,,,,,,,,,,,,,,,,,,,,,,,,,LawSchool ::: * myContacts,* Other,amartinez@kerncountylaw.org,,,,,,,,Mobile,+1 661-364-3180,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,','Adrianna "Gaby" Martinez'],
            'James (Created Name)' =>[',James,,Setaro,,,,,,,,,,,,,,,,,,,,,,,,,* myContacts,* ,james@haveitallhomes.com,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,','James Setaro']
        ];
    }
    
    /**
     * 
     * @param type $line
     * @group Sprint5
     * @dataProvider provideProblemLinesSprint5
     */
    public function testProblemLinesSprint5( $line, $test ){
        $parser = new GoogleCsvParser();
        $obj = $parser->getJsonObject(self::$header, $line );
        $this->assertTrue( $test($obj) );
    }
    
    public function provideProblemLinesSprint5(){
        $ana = <<<STRING
Ana Ovando,Ana,,Ovando,,,,,,,,,,,,,,,,,,,,,,"Criminal investigator, Keen County",,,LawSchool ::: * myContacts,* Home,aovando@kerncountylaw.org,,,,,,,,Mobile,+1 661-304-3165,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
STRING;
        $walsh = <<<STRING
Billy  Walsh,Billy,,Walsh,,,,,,,,,,,1976-08-18,,,,,,,,,,,,,https://lh5.googleusercontent.com/-nSOvx7FWaYI/WEBV5DBQJXI/AAAAAAAAAAA/dgMJdB_abnQLmZzKQwND3x1czzgzPNaoQCOQCEAE/photo.jpg,* myContacts,* Home,willybalsh@gmail.com,,,,,Home,Aim,willybalsh768,Mobile,917-806-4032,,,,,Home,"Los Angeles California 90036 United States ::: #3
627 N Spaulding Ave Los Angeles CA 90036 United States"," ::: #3
627 N Spaulding Ave",Los Angeles ::: Los Angeles, ::: ,CA ::: CA,90036 ::: 90036,United States ::: United States, ::: ,Work,8370 W 3rd St Los Angeles CA 90048 United States,8370 W 3rd St,Los Angeles,,CA,90048,United States,,Work,Self-Employed,,Managing Director,,,,,,,,
STRING;
        $winter = <<<STRING
Billy Winter,Billy,,Winter,,,,,,,,,,,,,,,,,,,,,,<HTCData><Facebook>id:100004967232876/friendof:10512107</Facebook></HTCData>,,,* myContacts,* Other,billywinter3@gmail.com,,,,,,,,Mobile,661-364-5625 ::: 661-319-1859,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
STRING;
        return [
            "Ana Ovando"=>[$ana, function($obj){
                return $obj->notes = "Criminal investigator, Keen County" &&
                        $obj->phones[0]->number == "+1 661-304-3165" &&
                        $obj->email == "aovando@kerncountylaw.org";
            }],
            "Billy Walsh" => [$walsh, function($obj){
                return $obj->notes == "" &&
                        $obj->address == "#3\n627 N Spaulding Ave; Los Angeles, CA 90036; United States"&&
                        $obj->phones[0]->number =="917-806-4032";
            }],
            "Billy Winter" => [$winter, function($obj){
                return $obj->phones[0]->number == "661-364-5625" &&
                $obj->phones[1]->number == "661-319-1859" &&
                $obj->address == "";
            } ]
        ];
    }
    
    /**
     * 
     * @param type $line
     * @group Sprint5
     */
    public function testLineParsingSprint5(){
        $ana = <<<STRING
Ana Ovando,Ana,,Ovando,,,,,,,,,,,,,,,,,,,,,,"Criminal investigator, Keen County",,,LawSchool ::: * myContacts,* Home,aovando@kerncountylaw.org,,,,,,,,Mobile,+1 661-304-3165,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
STRING;
        $walsh = <<<STRING
Billy  Walsh,Billy,,Walsh,,,,,,,,,,,1976-08-18,,,,,,,,,,,,,https://lh5.googleusercontent.com/-nSOvx7FWaYI/WEBV5DBQJXI/AAAAAAAAAAA/dgMJdB_abnQLmZzKQwND3x1czzgzPNaoQCOQCEAE/photo.jpg,* myContacts,* Home,willybalsh@gmail.com,,,,,Home,Aim,willybalsh768,Mobile,917-806-4032,,,,,Home,"Los Angeles California 90036 United States ::: #3
627 N Spaulding Ave Los Angeles CA 90036 United States"," ::: #3
627 N Spaulding Ave",Los Angeles ::: Los Angeles, ::: ,CA ::: CA,90036 ::: 90036,United States ::: United States, ::: ,Work,8370 W 3rd St Los Angeles CA 90048 United States,8370 W 3rd St,Los Angeles,,CA,90048,United States,,Work,Self-Employed,,Managing Director,,,,,,,,
STRING;
        $winter = <<<STRING
Billy Winter,Billy,,Winter,,,,,,,,,,,,,,,,,,,,,,<HTCData><Facebook>id:100004967232876/friendof:10512107</Facebook></HTCData>,,,* myContacts,* Other,billywinter3@gmail.com,,,,,,,,Mobile,661-364-5625 ::: 661-319-1859,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
STRING;
        $data = $ana . PHP_EOL. $walsh .PHP_EOL. $winter;
        $parser = new GoogleCsvParser();
        $lines = $parser->breakFileIntoLines($data);
        $this->assertEquals( 3, count($lines) );
        $this->assertEquals( "Ana Ovando,Ana,,Ovando", substr($lines[0],0,22));
        $this->assertEquals( "Billy  Walsh,Billy,,Wa", substr($lines[1],0,22));
        $this->assertEquals( "Billy Winter,Billy,,Wi", substr($lines[2],0,22));
    }
    
}
