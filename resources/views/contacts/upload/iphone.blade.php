@extends('main')

@section('main')
<div class="row">
    <div class="col-md-6 offset-md-3">
        <br/>
        <h1>Get Contacts from iOS devices</h1>
        <h2>Using a Mac</h2>
        <p>
            If you are on a Mac, open your Address Book. 
            Select all the contacts and drag-n-drop them into 
            Numbers or Mac Excel. From this program, you can save 
            the contact list in a CSV format.
        </p>
        <h2>Using a Windows PC</h2>
        <p>
            If you are on a PC, use <a href="https://www.copytrans.net/copytranscontacts/">copytrans contacts</a>. Run the program, plug 
            in your iPhone to your PC, and hit on the Export button. Next, choose 
            Excel as the export format.
        </p>
        <h2>When you have your file...</h2>
        <p><a href="{{route('upload_csv')}}" class="btn btn-primary">Upload CSV File</a></p>
    </div>
</div>
@endsection