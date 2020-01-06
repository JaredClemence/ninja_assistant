<?php

namespace App\Http\Controllers;

use App\ContactCsvFile;
use Illuminate\Http\Request;
use App\Http\Controllers\AbstractFactory\CsvFileControllerFactory;
use App\Http\Controllers\AbstractFactory\CsvFiles\AbstractController;
use App\Http\Controllers\UploadedFileController;
use App\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Clemence\Contact\IntermediateRecord;
use App\Jobs\ConvertCsvFileToIntermediateFile;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactUpdateIsComplete;

class ContactCsvFileController extends Controller {

    public function upload(Request $request) {
        $request->validate([
            'csv_upload'=>'required|file',
            'format'=>['required'],
            'terms'=>'accepted'
        ]);
        $uploadedFile = $this->storeFile($request);
        /* @var $uploadedFile \App\UploadedFile */
        $contactCsvFile = ContactCsvFile::create([
                    'user_id' => $uploadedFile->user_id,
                    'uploaded_file_id' => $uploadedFile->id,
                    'format'=> $request->input("format", "not set"),
                    'accepted_terms'=> $request->input("terms",0)=="on"
        ]);
        $contactCsvFile->save();
        ConvertCsvFileToIntermediateFile::dispatch($contactCsvFile);
        return redirect('/contacts/success');
    }
    
    public function uploadForm(Request $request){
        $formats = CsvFileControllerFactory::getAvailableFileFormats();
        return view('contacts.upload.upload', compact('formats'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showIntermediaries() {
        $user = Auth::user();
        $records = self::getIntermediariesNeedingApproval($user);
        return view('contacts.upload.preview',compact('records'));
    }
    
    public function deleteIntermediaries(IntermediateRecord $id){
        $id->delete();
        return back();
    }
    
    public function approveIntermediateRecords(){
        $user = Auth::user();
        $records = self::getIntermediariesNeedingApproval($user);
        $records->each( function($record){
            \App\Jobs\UpdateContactFromJson::dispatch($record);
        } );
        $completionEmail = new ContactUpdateIsComplete($user);
        Mail::queue($completionEmail);
        return redirect('/intermediates/success');
    }
    
    static function getIntermediariesNeedingApproval($user){
        
        $records = IntermediateRecord::where([
            ['user_id',$user->id],
            ['finished','=',0],
            ['json', '<>', '""']
        ])->get();
        return $records;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ContactCsvFile  $contactCsvFile
     * @return \Illuminate\Http\Response
     */
    public function show(ContactCsvFile $contactCsvFile) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ContactCsvFile  $contactCsvFile
     * @return \Illuminate\Http\Response
     */
    public function edit(ContactCsvFile $contactCsvFile) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ContactCsvFile  $contactCsvFile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ContactCsvFile $contactCsvFile) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ContactCsvFile  $contactCsvFile
     * @return \Illuminate\Http\Response
     */
    public function destroy(ContactCsvFile $contactCsvFile) {
        //
    }

    public static function delete(ContactCsvFile $contactCsvFile) {
        $controller = self::makeController($contactCsvFile);
        $controller->delete($contactCsvFile);
    }

    public static function archive(ContactCsvFile $contactCsvFile) {
        $controller = self::makeController($contactCsvFile);
        $controller->archive($contactCsvFile);
    }

    public static function process(ContactCsvFile $contactCsvFile, $testDelegate=null) {
        $controller = self::makeController($contactCsvFile);
        $controller->setDelegate($testDelegate);
        $controller->process($contactCsvFile);
    }

    private static function makeController(ContactCsvFile $contactCsvFile): AbstractController {
        $controller = CsvFileControllerFactory::make($contactCsvFile);
        return $controller;
    }

    private function storeFile($request): UploadedFile {
        return UploadedFileController::create($request, 'csv_upload');
    }

}
