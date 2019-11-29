@extends('main')

@section('main')
<div class="row">
    <div class="col-md-6 offset-md-3">
<h1>Upload CSV File</h1>
<p>Upload your contacts here. This will not overwrite existing records. 
    If a matching record exists, it will be expanded with new data.</p>
<div class="jumbotron">
    <form>
        @csrf
        <div class="form-group">
            <label><strong>Select a file:</strong></label>
            <input type="file" class="form-control-file"></input>
        </div>
        <div class="form-group">
            <label><strong>Format</strong></label>
            <select class="form-control">
                <option value="">Select a contact format</option>
                <option value="iphone">iPhone</option>
                <option value="android">Android</option>
            </select>
        </div>
        <button class="btn btn-primary" type="submit">Upload</button>
    </form>
</div>
    </div>
</div>
@endsection