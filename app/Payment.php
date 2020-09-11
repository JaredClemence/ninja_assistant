<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Subscriber;
use App\User;

class Payment extends Model
{
    use SoftDeletes;
    public function subscriber(){
        return $this->belongsTo(Subscriber::class);
    }
}
