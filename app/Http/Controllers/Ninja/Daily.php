<?php

namespace App\Http\Controllers\Ninja;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Carbon\Carbon;
use App\Contact;
use App\DailyData;

class Daily extends Controller {

    static public $session_prefix = "NINJA_DAILY_";
    static public $expire_hours = 15;

    /** @var User */
    private $user;
    
    public $didContact;
    private $contacts;
    public $calls;
    public $mail;
    static $call_count = 13;
    static $mail_count = 2;

    public function __construct(User $user) {
        $this->user = $user;
        $this->loadSessionData();
    }
    
    public function didContact(Contact $contact){
        return $this->didContact->contains($contact);
    }

    public function fresh() {
        $this->generateNewData();
        $this->saveSessionData();
    }

    public function replaceCaller($contact) {
        $list = clone $this->calls;
        $this->replace($contact, $list);
        $this->calls = $list;
        $this->saveSessionData();
    }

    public function replaceMailer($contact) {
        $list = clone $this->mail;
        $this->replace($contact, $list);
        $this->mail = $list;
        $this->saveSessionData();
    }

    private function loadSessionData() {
        $data = DailyData::initToday($this->user);
        if (count($data->readCallList())==0) {
            $this->fresh();
        } else {
            $callListData = $data->readCallList();
            $mailListData = $data->readMailList();
            $this->calls = $this->readContactIdArray($callListData);
            $this->mail = $this->readContactIdArray($mailListData);
        }
        $this->updateCallLogs();
    }

    private function generateNewData() {
        $this->initObjectVariables();
        $this->loadAvailableContacts();
        $this->pickRandomContactsToCall();
        $this->pickRandomContactsToMail();
    }

    private function saveSessionData() {
        $daily = DailyData::initToday($this->user);
        $daily->setCallList($this->calls);
        $daily->setMailList($this->mail);
        $daily->save();
    }

    private function replace($contact, \SplPriorityQueue $list) {
        $newList = new \SplPriorityQueue();
        $list->setExtractFlags(\SplPriorityQueue::EXTR_BOTH);
        foreach($list as $value){
            \extract($value);
            if($data->id == $contact->id){
                $data = $this->pickNewContact();
            }
            $newList->insert($data,$priority);
        }
        $newList->setExtractFlags(\SplPriorityQueue::EXTR_BOTH);
        $list->setExtractFlags(\SplPriorityQueue::EXTR_DATA);
        foreach($newList as $item){
            extract($item);
            $list->insert($data, $priority);
        }
    }

    private function pickNewContact() {
        $all = $this->getAvailableContacts();
        $callers = collect( clone $this->calls )->map( function($item){ return $item->id; } );
        $mailers = collect( clone $this->mail )->map( function($item){ return $item->id; } );
        $all = $all->reject( function($item) use ($callers, $mailers){
            return $callers->contains($item->id) || $mailers->contains($item->id);
        } );
        return $all->random();
    }
    
    private function readContactIdArray(&$array){
        $queue = new \SplPriorityQueue();
        $priority = 1000;
        if(is_array($array)){
            foreach($array as $contactId){
                $data = Contact::find($contactId); //update model to latest database entry
                if( $data !== null ){
                    $queue->insert($data, $priority--);
                }
            }
        }
        return $queue;
    }

    private function updateCallLogs() {
        $calls = clone( $this->calls );
        $mails = clone( $this->mail );
        $all = collect();
        foreach($calls as $call){ $all->add($call); }
        foreach($mails as $mail){ $all->add($mail); }
        $this->didContact = $all->filter(function($contact){ return $contact!==null; })->filter( function( $contact ){
            $latestNote = $contact->latestNote();
            if( $latestNote ){
                return Carbon::now()->subHours(24)->lt($latestNote->created_at);
            }else{
                return false;
            }
        } );
    }

    private function getAvailableContacts() {
        return Contact::where('active', '=', 1)->where('user_id',$this->user->id)->get();
    }

    private function initObjectVariables() {
        $this->contacts = new \SplObjectStorage;
        $this->calls = null;
        $this->mail = null;
    }

    private function loadAvailableContacts() {
        $this->contacts = $this->getAvailableContacts();
    }

    private function pickRandomContactsToCall() {
        $selectCallers = min($this->contacts->count(),self::$call_count);
        $this->calls = $this->contacts->random($selectCallers);
    }

    private function pickRandomContactsToMail() {
        $calls = $this->calls;
        $remaining = $this->contacts->reject(function($item) use ($calls) {
            return $calls->contains($item);
        });
        $selectMailers = min($remaining->count(), self::$mail_count);
        $this->mail = $remaining->random($selectMailers);
    }

}
