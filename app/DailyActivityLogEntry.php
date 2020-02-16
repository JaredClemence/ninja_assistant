<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Contact;

/**
 * @property int $user_id;
 * @property int $contact_id;
 * @property string $action;
 * @property string $family;
 * @property string $occupation;
 * @property string $recreation;
 * @property string $dreams;
 */
class DailyActivityLogEntry extends Model
{
    protected  $fillable = [
        'user_id',
        'contact_id',
        'action',
        'family',
        'occupation',
        'recreation',
        'dreams',
        'created_at',
    ];
    
    protected $casts = [
        'created_at'=>'datetime'
    ];
    
    public function user(){
        return $this->belongsTo(User::class);
    }
    
    public function contact(){
        return $this->belongsTo(Contact::class);
    }
}
