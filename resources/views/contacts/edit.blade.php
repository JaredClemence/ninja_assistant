@extends('main')
@section('main')
<form action='{{route('update_contact',['contact'=>$contact])}}' method='POST'>
    @csrf
    <fieldset>
        <legend>Contact Details</legend>
        <div class='form-group'>
            <label for='name'>Full Name</label>
            <input class='form-control' id='name' name='name' required='required' value='{{$contact->name}}' />
        </div>
        <div class='form-group'>
            <label for='note'>Note</label>
            <input class='form-control' id='note' name='note' value='{{$contact->note}}' placeholder='A short note' size='100' />
        </div>
        <div class='form-group'>
            <label for='address'>Address</label>
            <input class='form-control' id='address' name='address' value='{{$contact->address}}' placeholder='123 Main St; Bakersfield, CA 93301' size='150' />
        </div>
        <div class='form-group'>
            <label for='email'>Email</label>
            <input class='form-control' id='email' name='email' value='{{$contact->email}}' placeholder='user@domain.com' size='150' />
        </div>
    </fieldset>
    <fieldset>
        <legend>Phone Numbers</legend>
        @php
        $i = 1;
        @endphp
        @foreach($contact->phones as $phone)
        <fieldset>
            <legend>Phone #{{$i}}</legend>
            
        <div class='form-group'>
            <label>Phone Type</label>
            <input class='form-control' id='phone_name_{{$i}}' name='phone_name_{{$i}}' value='{{$phone->name}}' placeholder='Mobile/Home/Work' size='100' />
        </div>
        <div class='form-group'>
            <label>Phone Number</label>
            <input class='form-control' id='phone_number_{{$i}}' name='phone_number_{{$i}}' value='{{$phone->number}}' placeholder='(661) 555-0123' size='100' />
        </div>
            <input type='hidden' id='phone_id_{{$i}}' name='phone_id_{{$i}}' value='{{$phone->id}}' />
            </fieldset>
        @php
            $i++;
            @endphp
        @endforeach
        <fieldset>
            <legend>Phone #{{$i}} (Add a new number here)</legend>
            <div class='form-group'>
                <label>Phone Type</label>
                <input class='form-control' id='phone_name_{{$i}}' name='phone_name_{{$i}}' value='' placeholder='Mobile/Home/Work' size='100' />
            </div>
            <div class='form-group'>
                <label>Phone Number</label>
                <input class='form-control' id='phone_number_{{$i}}' name='phone_number_{{$i}}' value='' placeholder='(661) 555-0123' size='100' />
            </div>
            <input type='hidden' id='phone_id_{{$i}}' name='phone_id_{{$i}}' value='new' />
        </fieldset>
    </fieldset>
    <button type='submit' class='btn btn-primary btn-block'>Save Changes</button>
</form>
@endsection