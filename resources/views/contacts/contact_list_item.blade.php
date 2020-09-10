<div class='list-group-item'>
        <div class='row'>
            <div class='col-12 col-sm-3'>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="{{$contact->id}}" id='checkbox_{{$contact->id}}' name='checkbox_{{$contact->id}}'>
                    <label class="form-check-label" for="defaultCheck1">
                        @if( trim($contact->name) !='')
                        {{$contact->name}}
                        @else
                        <em>No Name on Record</em>
                        @endif
                    </label>
                </div>
                <br/>
                <em>Notes: {{$contact->note}}</em>
            </div>
            <div class='col-12 col-sm-4'>
                <em>
                    @if($contact->active) Eligible for FORD
                    @else Disabled for FORD
                    @endif
                </em>
                {{$contact->email}}<br/>
                {{$contact->address}}<br/>
                
            </div>
            <div class='col-12 col-sm-4'>
                <a href='{{route('contact.show',['contact'=>$contact])}}' class='btn btn-link'>Show Detail</a><br/>
                <a href='{{route('edit_contact',['contact'=>$contact])}}' class='btn btn-link'>Edit</a><br/>
                <a href='{{route('delete_contact',['contact'=>$contact])}}' class='btn btn-link'>Delete</a>
            </div>
        </div>
    </div>
