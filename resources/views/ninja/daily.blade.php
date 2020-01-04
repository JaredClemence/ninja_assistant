@extends('main')

@section('main')

<section>
    <h1>Daily Calls</h1>
    <div class='row'>
        <div class="col-12">
        <div class="list-group">
            @foreach($daily->calls as $caller)
            <div class="list-group-item">
                {{$caller->name}}<br/>
                @foreach($caller->phones as $phone)
                  @php
                    $number = $phone->number;
                    $type   = $phone->name;
                  @endphp
                  <a href="tel:{{$number}}">{{$type}}: {{$number}}</a><br/>
                @endforeach
                <a class="btn btn-secondary" href="{{route('edit_contact',['contact'=>$caller])}}">Edit</a><br/>
                <a class="btn btn-secondary" href="{{route('skip_contact',['contact'=>$caller])}}">Skip</a><br/>
                <a class="btn btn-secondary" href="{{route('deactivate_contact',['contact'=>$caller])}}">Deactivate</a><br/>
                <a class="btn btn-primary" href="{{route('create_activity_log',['contact'=>$caller,'action'=>'call'])}}">Make Notes</a>
                
            </div>
            @endforeach
        </div>
</div>
    </div>
</section>
<section>
    <h1>Daily Mailers</h1>
    <div class='row'>
        <div class="col-12">
    <div class="list-group">
            @foreach($daily->mail as $mailer)
            <div class="list-group-item">
                {{$mailer->name}}<br/>
                
                <a class="btn btn-secondary" href="{{route('edit_contact',['contact'=>$mailer])}}">Edit</a><br/>
                <a class="btn btn-secondary" href="{{route('skip_contact',['contact'=>$mailer])}}">Skip</a><br/>
                <a class="btn btn-secondary" href="{{route('deactivate_contact',['contact'=>$mailer])}}">Deactivate</a><br/>
                <a class="btn btn-primary" href="{{route('create_activity_log',['contact'=>$mailer,'action'=>'mail'])}}">Make Notes</a>
                
            </div>
            @endforeach
                    </div>
        </div>
    </div>
</section>
<section>
    <h1>Special Options</h1>
    <div class='row'>
        <div class="col-12">
            <a href="{{route('refresh_daily')}}" class="btn btn-danger">Regenerate Dailies</a><br/>
            
        </div>
    </div>
</section>
@endsection