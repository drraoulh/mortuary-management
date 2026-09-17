<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <a class="navbar-brand" href="/dashboard">
        🏥 Mortuary System
    </a>

    <div class="collapse navbar-collapse show">
        <ul class="navbar-nav me-auto">

            <li class="nav-item">
                <a class="nav-link" href="/dashboard">Dashboard</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="/deceased">Deceased</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('storage.index') }}">Storage Rooms</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('payments.index') }}">Payments</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('schedule.index') }}">Schedule</a>
            </li>
        </ul>
        @if(auth()->check() && auth()->user()->role === 'admin')
            <a class="nav-link text-white me-3" href="{{ route('admin.dashboard') }}">Admin</a>
        @endif

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-danger">
                Logout
            </button>
        </form>
    </div>
</nav>