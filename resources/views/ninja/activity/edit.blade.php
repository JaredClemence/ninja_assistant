@extends('main')
@section('main')
<form method='POST' action='{{route('edit_activity_log',['contact'=>$contact,'log'=>$log])}}' enctype="multipart/form-data">

    <div class='row'>
        <div class='col-12'>
            <h1>Contact</h1>
            <div>
                Name: <strong>{{$contact->name}}</strong><br/>
                Note: {{$contact->note}}
            </div>
            @include('ninja.activity.call_buttons')
        </div>
    </div>
    @include('ninja.activity.form_inputs')
</form>
@endsection