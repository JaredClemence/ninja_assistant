<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Ninja\Service;

use App\Contact;
use App\DailyActivityLogEntry;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Description of NinjaLogEntryService
 *
 * @author jaredclemence
 */
class NinjaLogEntryService {
    public function getTodaysLogEntry(Contact $contact, $action='call'){
        $params = $this->makeParamsArray($contact, $action);
        $entry = DailyActivityLogEntry::whereDate('created_at', Carbon::today())->where($params)->latest()->get()->first();
        return $entry;
    }

    public function getOrCreateTodaysLogEntry($contact, $action) : DailyActivityLogEntry {
        $entry = $this->getTodaysLogEntry($contact, $action);
        if( !$entry ){
            $params = $this->makeParamsArray($contact, $action);
            $entry = DailyActivityLogEntry::create($params);
        }
        return $entry;
    }
    
    public function byContactWithSimplePagination(Contact $contact, $paginateQt){
        return DailyActivityLogEntry::where('contact_id',$contact->id)->orderBy('created_at','desc')->simplePaginate($paginateQt);
    }

    public function getMostRecentLogEntryBeforeCurrent($contact, $currentEntry=null) {
        $params = [['contact_id','=',$contact->id]];
        if( $currentEntry ) $params[] = ['id','<>',$currentEntry->id];
        $last = DailyActivityLogEntry::where($params)->latest()->get()->first();
        return $last;
    }

    private function makeParamsArray($contact, $action) {
        $user = Auth::user();
        $params = [
            'user_id'=>$user->id,
            'contact_id'=>$contact->id,
            'action'=>$action
        ];
        return $params;
    }

    public function make() {
        return new DailyActivityLogEntry();
    }

}
