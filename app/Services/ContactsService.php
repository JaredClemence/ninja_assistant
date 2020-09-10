<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services;
use Traversable;
use App\Contact;

/**
 * Description of ContactsService
 *
 * @author jaredclemence
 */
class ContactsService {
    public function doAction( $action, $contact_ids ){
        switch( $action ){
            case 'Activate':
                $this->bulkActivate($contact_ids);
                break;
            case 'Deactivate':
                $this->bulkDeactivate($contact_ids);
                break;
            case 'Delete':
                $this->bulkDelete($contact_ids);
                break;
        }
    }

    private function bulkActivate($contact_ids) {
        Contact::whereIn('id',$contact_ids)->update(['active'=>1]);
    }

    private function bulkDeactivate($contact_ids) {
        Contact::whereIn('id',$contact_ids)->update(['active'=>0]);
    }

    private function bulkDelete($contact_ids) {
        Contact::whereIn('id',$contact_ids)->delete();
    }

}
