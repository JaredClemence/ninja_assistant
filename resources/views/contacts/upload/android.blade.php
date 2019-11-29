@extends('main')

@section('main')
<div class="row">
    <div class="col-md-6 offset-md-3">
<h1>Get Contacts from Android</h1>
<p>If you use an Android phone, then your contacts are managed by Google.</p>
<p>To export your contacts, log into <a href="//contacts.google.com">Google contacts</a>.
<p>Expand the menu on the left side of your screen. Scroll down to the export option. Click export.</p>
<p>Leave the default options in the popup window and click "Export" again. This will download your contacts to your device in a file named "contacts.csv".</p>
<p>Upload "contacts.csv" into the next screen to load them into this system.</p>
<p><a href="{{route('upload_csv')}}" class="btn btn-primary">Upload CSV File</a></p>
    </div>
</div>
@endsection