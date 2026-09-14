<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Mortuary Dashboard</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

    <div class="container">

        <a class="navbar-brand" href="{{ route('dashboard') }}">
            Mortuary System
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav me-auto">

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="{{ route('dashboard') }}"
                    >
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="{{ route('deceased.index') }}"
                    >
                        Deceased
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="{{ route('storage.index') }}"
                    >
                        Storage Rooms
                    </a>
                </li>

                {{-- PAYMENTS DROPDOWN --}}
                <li class="nav-item dropdown">

                    <a
                        class="nav-link dropdown-toggle"
                        href="#"
                        role="button"
                        data-bs-toggle="dropdown"
                    >
                        Payments
                    </a>

                    <ul class="dropdown-menu">

                        <li>
                            <a
                                class="dropdown-item"
                                href="{{ route('payments.index') }}"
                            >
                                My Payments
                            </a>
                        </li>

                        <li>
                            <a
                                class="dropdown-item"
                                href="{{ route('payments.create') }}"
                            >
                                Make Payment
                            </a>
                        </li>

                    </ul>

                </li>

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="{{ route('schedule.index') }}"
                    >
                        Schedule
                    </a>
                </li>

            </ul>

            {{-- USER / LOGOUT --}}

            <ul class="navbar-nav">

                <li class="nav-item dropdown">

                    <a
                        class="nav-link dropdown-toggle"
                        href="#"
                        role="button"
                        data-bs-toggle="dropdown"
                    >
                        {{ auth()->user()->name }}
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">

                        <li>

                            <form
                                method="POST"
                                action="{{ route('logout') }}"
                            >

                                @csrf

                                <button
                                    type="submit"
                                    class="dropdown-item"
                                >
                                    Logout
                                </button>

                            </form>

                        </li>

                    </ul>

                </li>

            </ul>

        </div>

    </div>

</nav>


{{-- DASHBOARD --}}

<div class="container mt-5">

    <h1 class="mb-4">
        Mortuary Dashboard
    </h1>


    {{-- THREE SUMMARY CARDS --}}

    <div class="row g-4">

        {{-- TOTAL DECEASED --}}

        <div class="col-md-4">

            <div class="card bg-primary text-white shadow">

                <div class="card-body">

                    <h5>
                        Total Deceased
                    </h5>

                    <h2>
                        {{ $totalDeceased }}
                    </h2>

                </div>

            </div>

        </div>


        {{-- AVAILABLE ROOMS --}}

        <div class="col-md-4">

            <div class="card bg-warning text-dark shadow">

                <div class="card-body">

                    <h5>
                        Available Rooms
                    </h5>

                    <h2>
                        {{ $availableRooms }}
                    </h2>

                </div>

            </div>

        </div>


        {{-- PENDING PAYMENTS --}}

        <div class="col-md-4">

            <div class="card bg-danger text-white shadow">

                <div class="card-body">

                    <h5>
                        Pending Payments
                    </h5>

                    <h2>
                        {{ $pendingPayments }}
                    </h2>

                </div>

            </div>

        </div>

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>