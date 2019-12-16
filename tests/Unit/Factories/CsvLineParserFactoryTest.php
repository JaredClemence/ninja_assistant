<?php

namespace Tests\Unit\Factories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Controllers\AbstractFactory\CsvLines\GoogleCsvParser;
use Tests\TestCase;
use App\Http\Controllers\AbstractFactory\CsvLineProcesserFactory;
use App\Http\Controllers\AbstractFactory\CsvLines\FileFormat;

class CsvLineParserFactoryTest extends TestCase
{   
    /**
     * @dataProvider dataProvider
     */
    public function testFormatParserSelection($format, $parser){
        $file = $this->makeFileWithFormat($format);
        $this->assertStateControllerIs( $file, $parser );
    }
    
    public function dataProvider(){
        $formats = CsvLineProcesserFactory::getAvailableFileFormats();
        $tests = [];
        foreach( $formats as $format ){
            /* @var $format FileFormat */
            $tests[$format->format] = [$format->format, $format->parser];
        }
        return $tests;
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
