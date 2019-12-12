<?php

namespace Tests\Unit\Factories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Controllers\AbstractFactory\CsvLines\NullCsvParser;
use App\Http\Controllers\AbstractFactory\CsvLines\AppleContactsCsvParser;
use App\Http\Controllers\AbstractFactory\CsvLines\GoogleCsvParser;
use Tests\TestCase;
use App\Http\Controllers\AbstractFactory\CsvLineProcesserFactory;

class CsvLineParserFactoryTest extends TestCase
{
    
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testReturnsNullController()
    {
        $file = $this->makeFileWithFormat( null );
        $this->assertStateControllerIs( $file, NullCsvParser::class );
    }
    
    public function testReturnsAppleContactsCsvParser(){
        $file = $this->makeFileWithFormat( 'iphone' );
        $this->assertStateControllerIs( $file, AppleContactsCsvParser::class );
    }
    
    public function testGoogleCsvParser(){
        $file = $this->makeFileWithFormat('android');
        $this->assertStateControllerIs( $file, GoogleCsvParser::class );
    }
    
    private function assertStateControllerIs( $file, $expectedClass ){
        $controller = CsvLineProcesserFactory::makeByFormat($file->format);
        $actualClass = get_class($controller);
        $this->assertEquals($expectedClass, $actualClass);
    }

    private function makeFileWithFormat($format) {
        $stdClass = new \stdClass();
        $stdClass->format = $format;
        return $stdClass;
    }

}
