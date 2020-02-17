@extends('main')
@section('main')
<div class='row'>
    <div class='col-12'>
        <h1>Activity Log</h1>
        @foreach($entries as $entry)
        <ul class="list-group">
            @include('templates.logEntryListItem')
        </ul>
        @endforeach
    </div>
</div>
@endsection