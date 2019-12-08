<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\UploadedFile;
use App\Http\Controllers\ContactCsvFileController;

class ContactCsvFile extends Model
{
    public function upload(){
        return $this->belongsTo( UploadedFile::class, 'uploaded_file_id' );
    }
    
    public function process(){
        ContactCsvFileController::process( $this );
    }
    
    public function archive(){
        ContactCsvFileController::archive( $this );
    }
    
    public function delete(){
        ContactCsvFileController::delete( $this );
    }
}
