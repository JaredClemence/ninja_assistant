@extends('main')
@section('main')
<div class='row'>
    <div class='col-12'>
        <h2>Verify The New Contact Records</h2>
        <p>Verify or cancel the transaction. Your contacts have not yet been changed.</p>
        <p>
            <a class='btn btn-primary' href='/intermediates/approve'>Approve Records</a>
        </p>
        <div class="list-group">
            @foreach($records as $record)
                @php
                $json = unserialize($record->json);
                $phones = array_map(function($phone){ return $phone->number; },$json->phones);
                $phonesHtml = join("<br/>", $phones);
                $data = [
                    'name'=>$json->name,
                    'phones'=>$phonesHtml,
                    'email'=>$json->email,
                    'address'=>$json->address,
                    'notes'=>$json->notes
                ];
                @endphp
                <div class="list-group-item">
                    <div class='row'>
                        <div class='col-12 col-sm-6'>
                    @foreach($data as $key=>$line)
                    @php
                    if( trim($line) == '' ) continue;
                    @endphp
                    {{$key}}: @if($key=='phones') {!!$line!!} @else {{$line}} @endif<br/>
                    @endforeach
                        </div>
                        <div class='col-12 col-sm-6'>
                            <a class='btn btn-danger btn-sm' href='/intermediate/{{$record->id}}/delete'>
                                Delete {{$json->name}}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection