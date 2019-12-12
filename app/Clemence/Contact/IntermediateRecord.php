<?php

namespace App\Clemence\Contact;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $header
 * @property string $line
 * @property string $json
 * @property string $format
 * @property boolean $finished
 * @property string $contact_csv_id
 */
class IntermediateRecord extends Model
{
    protected $fillable = [
        'header',
        'line',
        'json',
        'format',
        'finished',
        'uploaded_file_id',
        'user_id'
    ];
    protected $casts = [
        'finished'=>'boolean',
        'json'=>'json'
    ];
}
