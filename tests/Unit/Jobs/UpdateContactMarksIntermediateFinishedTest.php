<?php

namespace Tests\Unit\Jobs;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Jobs\UpdateContactFromJson;
use App\Clemence\Contact\IntermediateRecord;
use App\Http\Controllers\AbstractFactory\CsvLines\PhoneJsonObj;

/**
 * @group update_contact_job
 */
class UpdateContactMarksIntermediateFinishedTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testDoesFinish()
    {
        $record = $this->makeIntermediateRecord();
        UpdateContactFromJson::dispatchNow($record);
        $this->assertTrue( $record->finished, "The intermediate record has been marked finished.");
        return [$record];
    }
    
    /**
     * @depends testDoesFinish
     */
    public function testContactExists($params){
        $record = $params[0];
        $json = unserialize($record->json);
        $contacts = \App\Contact::where(['name'=>$json->name, 'user_id'=>$record->user_id])->get();
        $this->assertEquals(1, $contacts->count(),"There should only be one contact with the provided name.");
        $contact = $contacts[0];
        $this->assertEquals($record->contact_id,$contact->id,"The intermediate record should link to the contact record.");
    }

    private function makeIntermediateRecord() {
        $record = new IntermediateRecord();
        $record->user_id = 0;
        $record->format = $this->faker()->randomElement(['android','iphone','megazzi_one']);
        $record->header = "A,B,C,D,E,F";
        $record->line = "ANY TEXT HERE SHOULD NOT BE USED.";
        $record->json = serialize( $this->makeJsonObj() );
        return $record;
    }
    
    private function makeJsonObj(){
        $obj = new \stdClass();
        $obj->name = $this->faker()->name();
        $obj->address = $this->faker()->address;
        $obj->notes = "NOTES";
        $obj->email = $this->faker()->email;
        $obj->phones = [
            $this->makePhoneNumber($this->faker()->phoneNumber)
        ];
        return $obj;
    }

    private function makePhoneNumber($param0) {
        $phone = new PhoneJsonObj();
        $phone->number = $param0;
        $phone->type = $this->faker()->randomElement(['Mobile','Home','Work']);
        return $phone;
    }

}
