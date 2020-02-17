@extends('main')
@section('main')
<div class='row'>
    <div class='col-12'>
    <h1>Contact</h1>
    <div>
        Name: <strong>{{$contact->name}}</strong><br/>
        Note: {{$contact->note}}
    </div>
    </div>
</div>
<div class='row'>
    <div class='col-12'>
        <h1>Activity Log</h1>
        <div>
            Date: {{$log->created_at->format('l, F j')}}<br/>
            Action Type: {{$log->action}}
        </div>
        <form method='POST' action='{{route('edit_activity_log',['contact'=>$contact,'log'=>$log])}}' enctype="multipart/form-data">
            @csrf
            @foreach([
            'family'=>'Family',
            'occupation'=>'Occupation',
            'recreation'=>'Recreation',
            'dreams'=>'Dreams',
            ] as $key=>$varname)
            <div class='form-group'>
                <strong><label for='{{$key}}'>{{$varname}}:</label></strong><br/>
                @if(isset($last)&&$last!==null)
                @php
                $value = $last->{$key};
                @endphp
            {{$last->created_at->format('m/d/Y')}}: {{$value}}
            @else
            No previous notes for {{$varname}}
            @endif
                <input id='{{$key}}' name='{{$key}}' size='100' class='form-control' value='{{$log->{$key}!==null?$log->{$key}:""}}'/>
            </div>
            
            @endforeach
            <button class="btn btn-primary" type='submit'>Save</button>
        </form>
    </div>
</div>
@endsection