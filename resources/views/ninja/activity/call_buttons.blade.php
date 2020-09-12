@foreach($contact->phones as $phone)
    @php
    $number = $phone->number;
    $type   = $phone->name;
    @endphp
    <a href="tel:{{$number}}" class="btn btn-primary">Dial {{$type}}: {{$number}}</a>
@endforeach