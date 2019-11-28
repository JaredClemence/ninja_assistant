<?php

namespace App\Clemence;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id The primary key of the phone number record.
 * @property int $contact_id The primary id of the contact to which this number belongs.
 * @property string $number The number that should be dialed without formatting marks.
 * @property string $name An identifier to differentiate this number from others within the contact profile.
 * @property string $note An identifier to differentiate this number from others within the contact profile.
 */
class PhoneNumber extends Model
{
    protected $attributes = [
        'note' => '',
    ];
    
    protected $fillable = [
        'contact_id',
        'number',
        'name'
    ];
    public function contact(){
        return $this->belongsTo('App\Clemence\Contact');
    }
}
