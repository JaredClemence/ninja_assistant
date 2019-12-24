<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\UploadedFile;
use App\Http\Controllers\ContactCsvFileController;
use App\User;

/**
 * @property int $user_id The user who uploaded the document
 * @property int $uploaded_file_id The file that was uploaded.
 * @property string $format
 */
class ContactCsvFile extends Model
{
    protected $fillable = [
        'format',
        'user_id',
        'uploaded_file_id',
        'accepted_terms'
    ];
    
    public function upload(){
        return $this->belongsTo( UploadedFile::class, 'uploaded_file_id' );
    }
    
    public function user(){
        return $this->belongsTo( User::class );
    }
    
    public function process($testDelegate=null){
        ContactCsvFileController::process( $this, $testDelegate );
    }
    
    public function archive(){
        ContactCsvFileController::archive( $this );
    }
    
    public function delete(){
        ContactCsvFileController::delete( $this );
    }
}
