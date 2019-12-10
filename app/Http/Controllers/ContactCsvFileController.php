<?php

namespace App\Http\Controllers;

use App\ContactCsvFile;
use Illuminate\Http\Request;
use App\Http\Controllers\AbstractFactory\CsvFileControllerFactory;
use App\Http\Controllers\AbstractFactory\CsvFiles\AbstractController;
use App\Http\Controllers\UploadedFileController;
use App\UploadedFile;
use Illuminate\Support\Facades\Auth;

class ContactCsvFileController extends Controller {

    public function upload(Request $request) {
        $uploadedFile = $this->storeFile($request);
        /* @var $uploadedFile \App\UploadedFile */
        $contactCsvFile = ContactCsvFileController::create([
                    'user_id' => $uploadedFile->user_id,
                    'uploaded_file_id' => $uploadedFile->id
        ]);
        return redirect('/contacts/success');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //
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
        $controller = $this->makeController($contactCsvFile);
        $controller->delete($contactCsvFile);
    }

    public static function archive(ContactCsvFile $contactCsvFile) {
        $controller = $this->makeController($contactCsvFile);
        $controller->archive($contactCsvFile);
    }

    public static function process(ContactCsvFile $contactCsvFile) {
        $controller = $this->makeController($contactCsvFile);
        $controller->process($contactCsvFile);
    }

    private function makeController(ContactCsvFile $contactCsvFile): AbstractController {
        $controller = CsvFileControllerFactory::make($contactCsvFile);
        return $controller;
    }

    private function storeFile($request): UploadedFile {
        //$controller = new UploadedFileController();
        return $this->createUpload($request, 'csv_upload');
    }

    public function createUpload(Request $request, $file_identifier): UploadedFile {
        $user = Auth::user();
        if ($request->file($file_identifier)->isValid()) {
            $path = $request->file($file_identifier)->store('uploads');
            $name = basename($path);
            $uploadedFile = UploadedFile::create(
                            [
                                'user_id' => $user->id,
                                'name' => $name,
                                'full_path' => $path,
                            ]
            );
            $uploadedFile->save();
            return $uploadedFile;
        } else {
            dd('invalid');
        }
    }

}
