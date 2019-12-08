<?php

namespace Tests\Unit\Factories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Controllers\AbstractFactory\CsvFiles\NullController;
use App\Http\Controllers\AbstractFactory\CsvFiles\ArchivedController;
use App\Http\Controllers\AbstractFactory\CsvFiles\ProcessedController;
use App\Http\Controllers\AbstractFactory\CsvFiles\UnprocessedController;
use Tests\TestCase;
use App\Http\Controllers\AbstractFactory\CsvFileControllerFactory;

class ContactFileFactoryTest extends TestCase
{
    
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testReturnsNullController()
    {
        $this->assertStateControllerIs( null, NullController::class );
    }
    
    public function testReturnsUnprocessedController(){
        $file = $this->makeUploadWithState( null ); //unprocessed
        $this->assertStateControllerIs( $file, UnprocessedController::class );
    }
    
    public function testReturnsProcessedController(){
        $file = $this->makeUploadWithState('processed'); //unprocessed
        $this->assertStateControllerIs( $file, ProcessedController::class );
    }
    
    public function testReturnsArchivedController(){
        $file = $this->makeUploadWithState('archived'); //unprocessed
        $this->assertStateControllerIs( $file, ArchivedController::class );
    }

    private function makeUploadWithState($state) {
        $user = factory( \App\User::class )->create();
        $contactFile = factory( \App\ContactCsvFile::class )->make();
        $contactFile->user_id = $user->id;
        $file = $this->makeFileForState( $state );
        $contactFile->uploaded_file_id = $file->id;
        $contactFile->save();
        return $contactFile;
    }

    private function makeFileForState($state) {
        $file = null;
        if( $state ){
            $file = factory( \App\UploadedFile::class )->states($state)->create();
        }
        else {
            $file = factory( \App\UploadedFile::class )->create();
        }
        return $file;
    }

    public function assertStateControllerIs($contactCsvFile, $expectedClass) {
        $controller = CsvFileControllerFactory::make($contactCsvFile);
        $this->assertEquals($expectedClass, get_class($controller));
    }

}
