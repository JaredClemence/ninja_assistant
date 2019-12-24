<?php

namespace Tests\Unit\Jobs;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\ContactCsvFile;
use App\UploadedFile as SystemFileUp;
use Illuminate\Http\Testing\File;
use App\Jobs\ConvertCsvFileToIntermediateFile;
use App\Jobs\ConvertIntermideataryToJson;
use Exception;
use App\Http\Controllers\AbstractFactory\CsvLines\GoogleCsvParser;

class ProcessCsvFileTest extends TestCase {

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testSuccessfulFileMarksProcessComplete() {
        Storage::fake();
        Queue::fake();
        $self = $this;
        $delegate = $this->makeDelegate(
                function ($lines) use ($self) {
            $self->assertLineCount($lines, 4);
        }, function ($header) use ($self) {
            $content = "Name,Given Name,Additional Name,Family Name,Yomi Name,Given Name Yomi,Additional Name Yomi,Family Name Yomi,Name Prefix,Name Suffix,Initials,Nickname,Short Name,Maiden Name,Birthday,Gender,Location,Billing Information,Directory Server,Mileage,Occupation,Hobby,Sensitivity,Priority,Subject,Notes,Language,Photo,Group Membership,E-mail 1 - Type,E-mail 1 - Value,E-mail 2 - Type,E-mail 2 - Value,E-mail 3 - Type,E-mail 3 - Value,IM 1 - Type,IM 1 - Service,IM 1 - Value,Phone 1 - Type,Phone 1 - Value,Phone 2 - Type,Phone 2 - Value,Phone 3 - Type,Phone 3 - Value,Address 1 - Type,Address 1 - Formatted,Address 1 - Street,Address 1 - City,Address 1 - PO Box,Address 1 - Region,Address 1 - Postal Code,Address 1 - Country,Address 1 - Extended Address,Address 2 - Type,Address 2 - Formatted,Address 2 - Street,Address 2 - City,Address 2 - PO Box,Address 2 - Region,Address 2 - Postal Code,Address 2 - Country,Address 2 - Extended Address,Organization 1 - Type,Organization 1 - Name,Organization 1 - Yomi Name,Organization 1 - Title,Organization 1 - Department,Organization 1 - Symbol,Organization 1 - Location,Organization 1 - Job Description,Relation 1 - Type,Relation 1 - Value,Website 1 - Type,Website 1 - Value";
            $self->assertTextEquals($header, $content);
        }
        );
        $job = $this->makeGoodJob($delegate);
        $noException = true;

        try {
            $job->handle();
        } catch (Exception $exception) {
            $noException = false;
            dd($exception);
        }
        $this->assertIntermediatesCreated();
        $this->assertFileIsProcessed();
        $this->assertTrue($noException);
        Queue::assertPushed(ConvertIntermideataryToJson::class, 3);
    }

    public function assertLineCount($actual, $expected) {
        $this->assertEquals($expected, $actual);
    }

    public function assertTextEquals($actual, $expected) {
        $this->assertEquals($expected, $actual);
    }

    private function makeGoodJob($delegate = null) {
        $contactFile = $this->makeContactFile();
        $job = new ConvertCsvFileToIntermediateFile($contactFile, $delegate);
        return $job;
    }

    private function assertIntermediatesCreated() {
        
    }

    private function assertFileIsProcessed() {
        
    }

    private function makeContactFile(): ContactCsvFile {
        $content = <<<TEST
Name,Given Name,Additional Name,Family Name,Yomi Name,Given Name Yomi,Additional Name Yomi,Family Name Yomi,Name Prefix,Name Suffix,Initials,Nickname,Short Name,Maiden Name,Birthday,Gender,Location,Billing Information,Directory Server,Mileage,Occupation,Hobby,Sensitivity,Priority,Subject,Notes,Language,Photo,Group Membership,E-mail 1 - Type,E-mail 1 - Value,E-mail 2 - Type,E-mail 2 - Value,E-mail 3 - Type,E-mail 3 - Value,IM 1 - Type,IM 1 - Service,IM 1 - Value,Phone 1 - Type,Phone 1 - Value,Phone 2 - Type,Phone 2 - Value,Phone 3 - Type,Phone 3 - Value,Address 1 - Type,Address 1 - Formatted,Address 1 - Street,Address 1 - City,Address 1 - PO Box,Address 1 - Region,Address 1 - Postal Code,Address 1 - Country,Address 1 - Extended Address,Address 2 - Type,Address 2 - Formatted,Address 2 - Street,Address 2 - City,Address 2 - PO Box,Address 2 - Region,Address 2 - Postal Code,Address 2 - Country,Address 2 - Extended Address,Organization 1 - Type,Organization 1 - Name,Organization 1 - Yomi Name,Organization 1 - Title,Organization 1 - Department,Organization 1 - Symbol,Organization 1 - Location,Organization 1 - Job Description,Relation 1 - Type,Relation 1 - Value,Website 1 - Type,Website 1 - Value
Aaron Cobar,Aaron,,Cobar,,,,,,,,,,,,,,,,,,,,,,,,,* myContacts,,,,,,,,,,Mobile,(661) 808-3650,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
"Adrianna ""Gaby"" Martinez","Adrianna ""Gaby""",,Martinez,,,,,,,,,,,,,,,,,,,,,,,,,LawSchool ::: * myContacts,* Other,amartinez@kerncountylaw.org,,,,,,,,Mobile,+1 661-364-3180,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
Ben Sheldon,Ben,,Sheldon,,,,,,,,,,,,,,,,,,,,,,,,https://lh3.googleusercontent.com/-78BsBitcEos/WEBWXFrymwI/AAAAAAAAAAA/4h2F0DIQ3QIRu2h4b-hk_BcpJE1Ho9RZgCOQCEAE/photo.jpg,* myContacts,,,,,,,,,,Mobile,702-612-8536,Work,+1 702-209-8278,,,,,,,,,,,,,,,,,,,,,,Risoldi's ::: Risoldi's, ::: , ::: , ::: , ::: , ::: , ::: ,,,,
TEST;
        $filename = "test.csv";
        $path = "uploads/$filename";
        Storage::put('uploads/test.csv', $content);
        /* @var $uploadedFakeFile File */
        $file = new SystemFileUp();
        $file->fill([
            'user_id' => 1,
            'name' => $filename,
            'full_path' => $path
        ]);
        $file->save();
        $contactCsv = new ContactCsvFile();
        $contactCsv->fill([
            'format' => GoogleCsvParser::$format,
            'user_id' => 1,
            'uploaded_file_id' => $file->id,
            'accepted_terms' => true
        ]);
        $contactCsv->save();
        return $contactCsv;
    }

    public function makeDelegate($lineCountFunc, $reportHeaderFunc) {
        $delegate = new class($lineCountFunc, $reportHeaderFunc) {

            private $funcA;
            private $funcB;

            public function __construct($lineCountFunc, $reportHeaderFunc) {
                $this->funcA = $lineCountFunc;
                $this->funcB = $reportHeaderFunc;
            }

            public function reportLinesCount($a) {
                $funcA = $this->funcA;
                $funcA($a);
            }

            public function reportHeader($b) {
                $funcB = $this->funcB;
                $funcB($b);
            }
        };
        return $delegate;
    }

}
