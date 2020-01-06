@extends('main')

@section('main')

<section>
    <h1>Daily Calls</h1>
    @if($daily->calls->count()==0)
    <p>No callers in the list.</p>
    @endif
    <div class='row'>
        <div class="col-12">
        <div class="list-group">
            @foreach($daily->calls as $caller)
            <div class="list-group-item">
                <section class="col-12">
                    <div class="row">
                
                    <section class="col-12 col-sm-6">
                {{$caller->name}}
                    </section>
                    <section class="col-12 col-sm-6">
                @foreach($caller->phones as $phone)
                  @php
                    $number = $phone->number;
                    $type   = $phone->name;
                  @endphp
                  <a href="tel:{{$number}}" class="btn btn-secondary btn-block">Dial {{$type}}: {{$number}}</a>
                @endforeach
                  <a class="btn btn-primary btn-block" href="{{route('create_activity_log',['contact'=>$caller,'action'=>'call'])}}">Make Notes</a>
                    </section>
                    </div>
                </section>
                <section class="col-12">
                <a class="btn btn-link" href="{{route('edit_contact',['contact'=>$caller])}}">Edit</a>
                <a class="btn btn-link" href="{{route('skip_contact',['contact'=>$caller])}}">Skip</a>
                <a class="btn btn-link" href="{{route('deactivate_contact',['contact'=>$caller])}}">Deactivate</a>
                </section>
                
            </div>
            @endforeach
        </div>
</div>
    </div>
</section>
<section>
    <h1>Daily Mailers</h1>
    @if($daily->mail->count()==0)
    <p>No mailers in the list.</p>
    @endif
    <div class='row'>
        <div class="col-12">
    <div class="list-group">
            @foreach($daily->mail as $mailer)
            <div class="list-group-item">
                <div class="col-12">
                {{$mailer->name}}<br/>
                {{$mailer->address}}
                </div>
                <div class="col-12">
                <a class="btn btn-primary btn-block" href="{{route('create_activity_log',['contact'=>$mailer,'action'=>'mail'])}}">Make Notes</a>
                </div>
                <div class="col-12">
                <a class="btn btn-secondary" href="{{route('edit_contact',['contact'=>$mailer])}}">Edit</a>
                <a class="btn btn-secondary" href="{{route('skip_contact',['contact'=>$mailer])}}">Skip</a>
                <a class="btn btn-secondary" href="{{route('deactivate_contact',['contact'=>$mailer])}}">Deactivate</a>
                </div>
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