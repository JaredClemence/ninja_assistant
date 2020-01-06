<?php

namespace App\Http\Controllers\Ninja;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Carbon\Carbon;
use App\Contact;

class Daily extends Controller {

    static public $session_prefix = "NINJA_DAILY_";
    static public $expire_hours = 15;

    /** @var User */
    private $user;

    /** @var SplObjectStorage */
    private $contacts;

    /** @var SplPriorityQueue */
    public $calls;

    /** @var SplPriorityQueue */
    public $mail;
    static $call_count = 13;
    static $mail_count = 2;

    public function __construct(User $user) {
        $this->user = $user;
        $this->loadSessionData();
    }

    public function fresh() {
        $this->generateNewData();
        $this->saveSessionData();
        $this->saveExpirationData();
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
        $now = new Carbon("now");
        $expires = $this->readSession("expires");
        if ($expires === null || $now->gt($expires)) {
            $this->fresh();
        } else {
            $this->contacts = $this->readSession("contacts");
            $this->calls = $this->readPriorityQueueFromSession("calls");
            $this->mail = $this->readPriorityQueueFromSession("mail");
        }
    }

    private function readSession($name) {
        $value = unserialize(session(self::$session_prefix . $name));
        if ($value === false)
            $value = null;
        return $value;
    }

    private function writeSession($name, $serializableData) {
        session([self::$session_prefix . $name => serialize($serializableData)]);
    }

    private function generateNewData() {
        $this->contacts = $contacts = new \SplObjectStorage();
        $this->calls = $callQueue = new \SplPriorityQueue();
        $this->mail = $mailQueue = new \SplPriorityQueue();

        $all = Contact::where('active', '=', 1)->get();
        foreach ($all as $contact) {
            $contacts->attach($contact);
        }
        $selectCallers = min($all->count(),self::$call_count);
        $calls = $all->random($selectCallers);
        $remaining = $all->reject(function($item) use ($calls) {
            return $calls->contains($item);
        });
        $selectMailers = min($remaining->count(), self::$mail_count);
        $mail = $remaining->random($selectMailers);
        foreach ($calls as $call) {
            $rand = rand(0, 1000);
            $callQueue->insert($call, $rand);
        }
        foreach ($mail as $mailItem) {
            $rand = rand(0, 1000);
            $mailQueue->insert($mailItem, $rand);
        }
    }

    private function saveSessionData() {
        $this->writeSession("contacts", $this->contacts);
        $this->writePriorityQueueToSession("calls", $this->calls);
        $this->writePriorityQueueToSession("mail",$this->mail);
    }

    private function saveExpirationData() {
        $expireDate = new Carbon("now");
        $expireDate->addHours( self::$expire_hours );
        $this->writeSession("expires",$expireDate);
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
        $all = collect( clone $this->contacts );
        $callers = collect( clone $this->calls )->map( function($item){ return $item->id; } );
        $mailers = collect( clone $this->mail )->map( function($item){ return $item->id; } );
        $all = $all->reject( function($item) use ($callers, $mailers){
            return $callers->contains($item->id) || $mailers->contains($item->id);
        } );
        return $all->random();
    }

    private function writePriorityQueueToSession($name, $queue) {
        $array = [];
        $source = clone $queue;
        /* @var $source \SplPriorityQueue */
        $source->setExtractFlags(\SplPriorityQueue::EXTR_BOTH);
        foreach($source as $item ){
            $array[]=$item;
        }
        $this->writeSession($name, $array);
    }
    
    private function readPriorityQueueFromSession($name){
        $queue = new \SplPriorityQueue();
        $array = $this->readSession($name);
        if(is_array($array)){
            foreach($array as $item){
                \extract($item);
                $data = $data->find($data->id); //update model to latest database entry
                $queue->insert($data, $priority);
            }
        }
        return $queue;
    }

}
