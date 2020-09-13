@extends('main')
@section('main')
<form method='POST' action='{{route('edit_activity_log',['contact'=>$contact,'log'=>$log])}}' enctype="multipart/form-data">

    <div class='row'>
        <div class='col-12'>
            <a class="btn btn-link" href="{{route('edit_contact',['contact'=>$contact])}}">Edit</a>
            <a class="btn btn-link" href="{{route('skip_contact',['contact'=>$contact])}}">Skip</a>
            <a class="btn btn-link" href="{{route('deactivate_contact',['contact'=>$contact])}}">Deactivate</a>
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