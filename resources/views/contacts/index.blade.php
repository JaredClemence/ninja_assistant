@extends('main')
@section('main')
<div class='list-group'>
    @foreach($all as $contact)
    <div class='list-group-item'>
    <div class='row'>
        <div class='col-12'>
            {{$contact->name}}<br/>
            <em>{{$contact->note}}</em>
        </div>
        <div class='col-12 col-sm-8'>
            {{$contact->email}}<br/>
            {{$contact->address}}<br/>
            <em>
            @if($contact->active) Active Contact
            @else Hidden Contact
            @endif
            </em>
        </div>
        <div class='col-12 col-sm-4'>
            <a href='{{route('edit_contact',['contact'=>$contact])}}' class='btn btn-link'>Edit</a><br/>
            <a href='{{route('delete_contact',['contact'=>$contact])}}' class='btn btn-link'>Delete</a>
        </div>
    </div>
    </div>
    @endforeach
</div>
@endsection