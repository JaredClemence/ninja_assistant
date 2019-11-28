<?php

namespace App\Clemence;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name Unique identifier for contact record
 * @property string $note Notes on the contact.
 */
class Contact extends Model
{
    protected $attributes = [
        'note' => '',
        'address'=>''
    ];
    
    protected $fillable = [
        'name',
        'note',
        'address'
    ];
    
    public function phone_numbers(){
        return $this->hasMany('App\Clemence\PhoneNumber');
    }
}
