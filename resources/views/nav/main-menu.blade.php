<nav class="navbar navbar-expand-md navbar-dark bg-dark">  
  <a class="navbar-brand" href="{{ url('/') }}">
    {{ config('app.name', 'Laravel') }}
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample04" aria-controls="navbarsExample04" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarsExample04">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link" href="/">Home</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="dropdown04" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Contacts</a>
        <div class="dropdown-menu" aria-labelledby="dropdown04">
          <a class="dropdown-item" href="{{route('create_contact')}}">Create Contact</a>
          <a class="dropdown-item" href="{{route('index_contacts')}}">Show Contacts</a>
          <a class="dropdown-item" href="{{route('upload_csv')}}">Upload Contacts</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('activity.list')}}">Activity Log</a>
      </li>
    </ul>
  </div>
</nav>