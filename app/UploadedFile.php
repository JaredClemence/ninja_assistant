<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Carbon\Carbon;

/**
 * @property string $name File name
 * @property string $full_path Full path to storage location
 * @property int $user_id The user who uploaded the document
 * @property boolean $process_date
 * @property boolean $processed
 * @property boolean $archived
 */
class UploadedFile extends Model
{
    protected $fillable = [
        'user_id',
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
    
    public function user(){
        return $this->belongsTo( User::class );
    }

    public function markProcessed() {
        $this->processed = true;
        $this->process_date = new Carbon("now");
    }

}
