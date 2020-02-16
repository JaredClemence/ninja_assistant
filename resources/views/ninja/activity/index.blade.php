@extends('main')
@section('main')
<div class='row'>
    <div class='col-12'>
        <h1>Activity Log</h1>
        @foreach($entries as $entry)
        <ul class="list-group">
            <li class="list-group-item">
                <div class='row'>
                    <div class='col-12 col-md-2'>
                        {{$entry->created_at->format('l, M d, Y')}}
                    </div>
                    <div class='col-12 col-md'>
                        <strong>Contact Name:</strong> {{$entry->contact->name}}
                        <span class='d-block'><br/></span>
                    </div>
                    <div class='col-12 col-md'>
                        <strong>Family:</strong> {{$entry->family}}<br/>
                        <strong>Occupation:</strong> {{$entry->occupation}}<br/>
                        <strong>Recreation:</strong> {{$entry->recreation}}<br/>
                        <strong>Dreams:</strong> {{$entry->dreams}}<br/>
                        <strong>User ID:</strong> {{$entry->user_id}}      
                    </div>
                </div>
            </li>
        </ul>
        @endforeach
    </div>
</div>
@endsection