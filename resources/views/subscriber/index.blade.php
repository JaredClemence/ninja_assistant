@extends('main')
@section('main')
{{$users->links()}}
<div class="list-group">
    <div class='list-group-item'>
        <div class='row'>
        <div class='col-6'>
            <strong>Subscriber Name</strong>
        </div>
        <div class='col-6'>
            <strong>Expires</strong>
        </div>
        </div>
    </div>
    @foreach( $users as $user )
    <div class='list-group-item'>
        <div class='row'>
        <div class='col-6'>
            {{$user->name}}
        </div>
        <div class='col-6'>
            {{$user->subscriber->expire->format("M d Y")}}
        </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
