<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Clemence\PhoneNumber;
use App\DailyActivityLogEntry;

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
    protected $with = [
        'phones'
    ];
    
    protected $fillable = [
        'note',
        'email',
        'address',
        'active',
        'name'
    ];
    
    public function notes(){
        return $this->hasMany(DailyActivityLogEntry::class);
    }
    
    public function latestNote(){
        return DailyActivityLogEntry::where('contact_id','=', $this->id)->latest()->get()->first();
    }


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
