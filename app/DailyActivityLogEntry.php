<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
        'dreams'
    ];
}
