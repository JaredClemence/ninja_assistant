@extends('main')

@section('main')
<div class="jumbotron">
      <div class="col-sm-8 mx-auto">
        <h1>Upload Contacts</h1>
        <p>Get instructions for your device.</p>
        <p>
          <a class="btn btn-primary" href="{{ route('android_instruction') }}" role="button">Get Android instructions ...</a>
        </p>
        <p>
          <a class="btn btn-primary" href="{{ route('iphone_instruction') }}" role="button">Get iOS instructions ...</a>
        </p>
        <p>
          <a class="btn btn-primary" href="{{ route('upload_csv') }}" role="button">Upload CSV file ...</a>
        </p>
      </div>
    </div>
@endsection