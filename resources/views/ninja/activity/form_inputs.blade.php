<div class='row'>
    <div class='col-12'>
        <h1>Activity Log</h1>
        <div>
            Date: @if($log->created_at)
            {{$log->created_at->format('l, F j')}}
            @else
            {{(new \DateTime())->format('l, F j')}}
            @endif<br/>
            Action Type: {{$log->action}}
        </div>
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
                <input id='{{$key}}' name='{{$key}}' size='100' class='form-control ninja-data-field' value='{{$log->{$key}!==null?$log->{$key}:""}}'/>
            </div>
            
            @endforeach
            <button class="btn btn-primary" type='submit'>Contact Successful</button>
            <a href="{{route('daily_call')}}" class="btn btn-secondary">No Contact</a>
            <a href="javascript:leave_message()" class="btn btn-secondary">Left Message</a>
            <script type="text/javascript">
                function leave_message(){
                    $('.ninja-data-field').val('Left voicemale.');
                    
                }
                
            </script>
    </div>
</div>