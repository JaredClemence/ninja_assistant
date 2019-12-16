<?php

namespace Tests\Unit\Factories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Controllers\AbstractFactory\CsvLines\MagazziOneParser;

class MagazziOneLineParserTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testBreakIntoLines()
    {
        $this->assertTrue(true);
    }
    
    /**
     * 
     * @param type $text
     * @param type $name
     * @param type $address
     * @param type $note
     * @param type $email
     * @param type $phone
     * @dataProvider provideForGetFields
     */
    public function testGetFields($lineText, $lastName, $firstName, $street, $city, $state, $zip, $note, $email, $phone){
        $parser = new MagazziOneParser();
        $fields = $parser->getFieldsFromCsvLine($lineText);
        $this->assertEquals($lastName, $fields[0]);
        $this->assertEquals($firstName, $fields[1]);
        $this->assertEquals($street, $fields[2]);
        $this->assertEquals($city, $fields[3]);
        $this->assertEquals($state, $fields[4]);
        $this->assertEquals($zip, $fields[5]);
        $this->assertEquals($note, $fields[7]);
        $this->assertEquals($email, $fields[8]);
        $this->assertEquals($phone, $fields[9]);
        
    }
    
    public function provideForGetFields(){
        return [
            [
                'Aase ,Adam & Marsha,12414 Schooner Beach Dr,Bakersfield ,CA,93311,,,,,,,,,,,,',
                'Aase',
                'Adam & Marsha',
                '12414 Schooner Beach Dr',
                'Bakersfield',
                'CA',
                '93311',
                '',
                '',
                ''
            ],
            [
                'Banker,Chris and Rachel,16640 Stephenie St,Bakersfield,CA,93314,friend ,new baby girl ,,661-900-6612,,,,,,,,',
                'Banker',
                'Chris and Rachel',
                '16640 Stephenie St',
                'Bakersfield',
                'CA',
                '93314',
                'new baby girl',
                '',
                '661-900-6612'
            ],
            [
                'Black ,Ed and Anna ,3061 Elmwood Ave ,Bakersfield ,CA ,93305,realtor.com/school,,"aolsson1984@gmail.com,mrblackebd@gmail.com",,,,,,,,,',
                'Black',
                'Ed and Anna',
                '3061 Elmwood Ave',
                'Bakersfield',
                'CA',
                '93305',
                '',
                'aolsson1984@gmail.com,mrblackebd@gmail.com',
                ''
            ]
        ];
    }
    
    public function testGetHeader(){
        $parser = new MagazziOneParser();
        $this->assertEquals( MagazziOneParser::$header, $parser->getHeaderFromFileContnent('ANY CONTENT'));
    }
    
    /**
     * @param string $lineText
     * @param string $name
     * @param string $address
     * @param string $note
     * @param string $phone
     * @param string $email
     * @dataProvider provideForGetJsonObject
     */
    public function testGetJsonObject($lineText, $name, $address, $note, $phone, $email){
        $parser = new MagazziOneParser();
        $obj = $parser->getJsonObject(MagazziOneParser::$header, $lineText);
        $this->assertEquals($name, $obj->name);
        $this->assertEquals($address, $obj->address);
        $this->assertEquals($email, $obj->email);
        $this->assertEquals($note, $obj->notes);
        if( $phone ){
            $this->assertEquals($phone, $obj->phones[0]->number);
        }
    }
    
    public function provideForGetJsonObject(){
        return [
            [
                'Aase ,Adam & Marsha,12414 Schooner Beach Dr,Bakersfield ,CA,93311,,,,,,,,,,,,',
                'Adam & Marsha Aase',
                '12414 Schooner Beach Dr; Bakersfield, CA 93311',
                '',
                '',
                ''
            ],
            [
                'Banker,Chris and Rachel,16640 Stephenie St,Bakersfield,CA,93314,friend ,new baby girl ,,661-900-6612,,,,,,,,',
                'Chris and Rachel Banker',
                '16640 Stephenie St; Bakersfield, CA 93314',
                'friend new baby girl',
                '661-900-6612',
                ''
            ],
            [
                'Black ,Ed and Anna ,3061 Elmwood Ave ,Bakersfield ,CA ,93305,realtor.com/school,,"aolsson1984@gmail.com,mrblackebd@gmail.com",,,,,,,,,',
                'Ed and Anna Black',
                '3061 Elmwood Ave; Bakersfield, CA 93305',
                'realtor.com/school',
                '',
                'aolsson1984@gmail.com,mrblackebd@gmail.com'
            ]
        ];
    }
    public function testGetFormatObject(){
        $format = MagazziOneParser::makeFormat();
        $this->assertEquals(MagazziOneParser::class, $format->parser, "The class name does not match on the format object.");
        $this->assertEquals(MagazziOneParser::$format, $format->format, "The format does not match on the format object.");
        $this->assertEquals(MagazziOneParser::$label,$format->label, "The label does not match on the format object.");
    }
}
