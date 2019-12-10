@extends('main')

@section('main')
<div class="row">
    @if($errors->any() || true)
    <div class="col-12">
        <br/>
        <ul class="list-group">
            @foreach($errors->all() as $error )
            <li class="list-group-item list-group-item-danger">
                {{$error}}
            </li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="col-md-6 offset-md-3">
        <br/>
        <h1>Upload CSV File</h1>
        <p>Upload your contacts here. This will not overwrite existing records. 
            If a matching record exists, it will be expanded with new data.</p>
        <div class="jumbotron">
            <form method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label><strong>Select a file:</strong></label>
                    <input type="file" name="csv_upload" class="form-control-file"></input>
                </div>
                <div class="form-group">
                    <label><strong>Format</strong></label>
                    <select name="format" class="form-control">
                        <option value="">Select a contact format</option>
                        <option value="iphone">iPhone</option>
                        <option value="android">Android</option>
                    </select>
                </div>
                <div class="form-check">
                    <input name="terms" type="checkbox" class="form-check-input" id="exampleCheck1">
                    <label class="form-check-label" for="exampleCheck1">
                        <strong>Terms:</strong> By uploading your contacts, you agree to allow this 
                        site to read the file and convert the file into data 
                        that you can use in the application. 
                        The site will retain a copy of the file for up to one 
                        year. Your contact data will not be used by any account 
                        other than the one that uploads it.</label>
                </div>
                <br/>
                <button class="btn btn-primary" type="submit">Upload</button>
            </form>
        </div>
    </div>
</div>
@endsection