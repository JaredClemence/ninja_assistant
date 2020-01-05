<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Clemence\Contact\IntermediateRecord;
use App\Contact;
use App\Clemence\PhoneNumber;
use App\Http\Controllers\AbstractFactory\CsvLines\ContactJsonObj;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UpdateContactFromJson implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $timeout = 500;

    /** @var IntermediateRecord */
    private $intermediateRecord;
    /** @var Contact */
    private $contact = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(IntermediateRecord $intermediateRecord)
    {
        $this->intermediateRecord = $intermediateRecord;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->markIntermediateFinished();
        $this->saveIntermediate();
        $this->loadExistingRecord();
        if( $this->getExistingRecord() == null ){
            $this->initializeANewRecord();
        }
        $this->updateRecord();
        $this->updateContactId();
        $this->saveRecord();
    }

    private function loadExistingRecord() {
        $jsonString = $this->intermediateRecord->json;
        $json = unserialize($jsonString);
        /* @var $json ContactJsonObj */
        $name = $json->name;
        $contacts = Contact::where([
            ['name','=',$name],
            ['user_id','=',$this->intermediateRecord->user_id]
        ])->get();
        if( $contacts->count() > 1 ){
            $this->mergeContacts( $contacts );
        }
        if( $contacts->count() >= 1 ){
            $this->contact = $contacts[0];
        }else{
            $this->contact = null;
        }
    }

    private function getExistingRecord() {
        return $this->contact;
    }

    private function initializeANewRecord() {
        $this->contact = new Contact();
        $this->contact->user_id = $this->intermediateRecord->user_id;
    }

    private function updateRecord() {
        $json = unserialize($this->intermediateRecord->json);
        /* @var $json ContactJsonObj */
        $this->contact->name = $json->name;
        $this->contact->email = $json->email;
        $this->contact->note = $json->notes;
        $this->contact->address = $json->address;
        $this->contact->save();
        foreach($json->phones as $phone){
            $number = $this->makePhoneNumber($phone);
            if( $this->contact->hasPhoneNumber( $number ) == false ){
                $this->contact->phones()->save($number);
            }
        }
    }

    private function saveRecord() {
        $this->contact->save();
    }

    private function markIntermediateFinished() {
        $this->intermediateRecord->finished = true;
    }

    private function saveIntermediate() {
        $this->intermediateRecord->save();
    }

    /**
     * @param type $contacts
     */
    private function mergeContacts($contacts) {
        $first = $contacts[0];
        foreach($contacts as $contact ){
            if( $first == $contact ) continue;
            $first->note .= "; " .$contact->note;
            $contact->delete();
        }
    }

    private function makePhoneNumber($phone) {
        $number = new PhoneNumber();
        $number->number = $phone->number;
        $number->name = $phone->type;
        return $number;
    }

    private function updateContactId() {
        $this->intermediateRecord->contact_id = $this->contact->id;
    }

}
