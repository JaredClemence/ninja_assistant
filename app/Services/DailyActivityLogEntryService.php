<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services;
use App\Contact;
use App\DailyActivityLogEntry;

/**
 * Description of DailyActivityLogEntryService
 *
 * @author jaredclemence
 */
class DailyActivityLogEntryService {
    public function byContactWithSimplePagination(Contact $contact, $paginateQt){
        return DailyActivityLogEntry::where('contact_id',$contact->id)->orderBy('created_at','desc')->simplePaginate($paginateQt);
    }
}
