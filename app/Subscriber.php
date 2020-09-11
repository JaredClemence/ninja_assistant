<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;
use App\Payment;

/**
 * @property User $user
 */
class Subscriber extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'expire'
    ];
    
    protected $casts = [
        'expire'=>'date'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function payments(){
        return $this->hasMany(Payment::class);
    }
}
