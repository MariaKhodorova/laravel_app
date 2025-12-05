<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">MyApp</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('form.show') }}">Форма</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('form.list') }}">Список</a></li>
            </ul>
        </div>
    </div>
</nav>
