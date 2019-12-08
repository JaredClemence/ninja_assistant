<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

/**
 * @property string $name File name
 * @property string $full_path Full path to storage location
 * @property boolean $process_date
 * @property boolean $processed
 * @property boolean $archived
 */
class UploadedFile extends Model
{
    protected $fillable = [
        'name',
        'full_path',
        'process_date',
        'processed',
        'archived'
    ];
    
    protected $casts = [
        'process_date'=>'datetime:Y-m-d',
        'processed'=>'boolean',
        'archived'=>'boolean'
    ];
}
