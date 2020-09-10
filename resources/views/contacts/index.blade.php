@extends('main')
@section('main')
<script>
    $(document).ready(function () {
        $("#filter").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            let listItems = $("#display .list-group-item");
            listItems.filter(function () {
                let text = $(this).text().toLowerCase();
                let elem = $(this);
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
</script>
<div class='form'>
    <div class='form-group'>
        <label for='filter'>Filter Contact List</label>
        <input type='text' class='form-control' id='filter' placeholder='type name here'/>
    </div>
</div>
<form action='{{route('contacts.bulk')}}' method='post'>
    @csrf
            <div class='form-group'>
                <label for='bulk_action'>Bulk Action</label>
                <select class='form-control' id='bulk_action' name='bulk_action'>
                    <option></option>
                    <option>Activate</option>
                    <option>Deactivate</option>
                    <option>Delete</option>
                </select>
            </div>
            <button type='submit' class='btn btn-primary'>Perform Bulk Action</button><br/>&nbsp;
    <div class='list-group' id='display'>
        @foreach($all as $contact)
        @include('contacts.contact_list_item')
        @endforeach
    </div>
</form>
@endsection