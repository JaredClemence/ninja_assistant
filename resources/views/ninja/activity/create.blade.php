@extends('main')
@section('main')
<form method='POST' action='{{route('create_activity_log',['contact'=>$contact,'action'=>$action])}}' enctype="multipart/form-data">
        
<div class='row'>
    <div class='col-12'>
    <h1>Contact</h1>
    <div>
        Name: <strong>{{$contact->name}}</strong><br/>
        Note: {{$contact->note}}
    </div>
    </div>
</div>
@include('ninja.activity.form_inputs')
        </form>
@endsection