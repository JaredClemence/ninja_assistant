<?php

namespace App\Http\Controllers;

use App\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UploadedFileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UploadedFile  $uploadedFile
     * @return \Illuminate\Http\Response
     */
    public function show(UploadedFile $uploadedFile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UploadedFile  $uploadedFile
     * @return \Illuminate\Http\Response
     */
    public function edit(UploadedFile $uploadedFile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UploadedFile  $uploadedFile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UploadedFile $uploadedFile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UploadedFile  $uploadedFile
     * @return \Illuminate\Http\Response
     */
    public function destroy(UploadedFile $uploadedFile)
    {
        //
    }

    public static function create(Request $request, $file_identifier) : UploadedFile {
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
