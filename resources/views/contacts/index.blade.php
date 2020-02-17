@extends('main')
@section('main')
<script>
$(document).ready(function(){
  $("#filter").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    let listItems = $("#display .list-group-item");
    listItems.filter(function() {
        let text = $(this).text().toLowerCase();
        let elem = $(this);
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
  });
});
</script>
<div class='form'>
    <div class='form-group'>
        <label for='filter'>Filter</label>
        <input type='text' class='form-control' id='filter' />
    </div>
</div>
<div class='list-group' id='display'>
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
            <a href='{{route('contact.show',['contact'=>$contact])}}' class='btn btn-link'>Show Detail</a><br/>
            <a href='{{route('edit_contact',['contact'=>$contact])}}' class='btn btn-link'>Edit</a><br/>
            <a href='{{route('delete_contact',['contact'=>$contact])}}' class='btn btn-link'>Delete</a>
        </div>
    </div>
    </div>
    @endforeach
</div>
@endsection