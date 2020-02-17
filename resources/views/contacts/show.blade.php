@extends('main')
@section('main')
<div class="row">
    <div class="col-12">
    <h1>{{$contact->name}} - <small>Contact Card</small></h1>
    </div>
    <div class='col-12'>
        <a class="button btn-lg btn-primary" href="{{route('create_activity_log',['contact'=>$contact,'action'=>'call'])}}">Make Call Record</a> 
        <a class="button btn-lg btn-info" href="{{route('create_activity_log',['contact'=>$contact,'action'=>'mail'])}}">Make Mail Record</a> 
    </div>
    <div class="col-12">
        <h2>Phones</h2>
        <ul class="list-group">
            @foreach($contact->phones as $phone)
            <li class="list-group-item">{{$phone->name}}: {{$phone->number}}</li>
            @endforeach
        </ul>
    </div>
    <div class="col-12">
        <h2>Mailing Address</h2>
        <ul class='list-group'>
            <li class='list-group-item'>
        @if( $contact->address == '' )
        No Address
        @else
        {{$contact->address}}
        @endif
            </li>
        </ul>
    </div>
    <div class="col-12">
        <h2>Contact History</h2>
        <ul class='list-group'>
            @foreach( $contact->logEntries as $entry )
            @include('templates.logEntryListItem')
            @endforeach
        </ul>
    </div>
</div>
@endsection