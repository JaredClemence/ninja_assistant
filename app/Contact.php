<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Clemence\PhoneNumber;

/**
 * @property string $name Person name
 * @property string $note
 * @property string $email
 * @property string $address String address as a single line.
 * @property boolean $active 
 * @property array $phones
 * @property int $user_id
 */
class Contact extends Model
{
    protected $with = ['phones'];
    
    public function phones(){
        return $this->hasMany(PhoneNumber::class);
    }
    public function hasPhoneNumber(PhoneNumber $contact){
        $number = $contact->number;
        $exists = false;
        foreach( $this->phones as $phone ){
            if($phone->number == $number){
                $exists = true;
                break;
            }
        }
        return $exists;
    }
}
