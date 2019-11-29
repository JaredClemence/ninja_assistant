@extends('bootstrap')

@section('body')
@include('nav.main-menu')
<div class="container">
  <main role="main">
    @yield('main')
  </main>
</div>
@endsection