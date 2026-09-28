@extends('layouts.app')

@section('content')

<div class="container mt-4">


<style>

body{
background:
linear-gradient(
135deg,
#0f172a,
#1e293b,
#334155
);

min-height:100vh;
}

.dashboard-card{
background:white;
border-radius:15px;
box-shadow:0 0 15px rgba(0,0,0,.2);
padding:20px;
}

</style>

<div class="admin-bg">

<div class="container py-5">

<h1 class="text-white fw-bold mb-4">
    Administrator Dashboard
</h1>

<div class="row">

    <div class="col-md-3 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5>Total Deceased</h5>
                <h2>{{ $totalDeceased }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5>Total Payments</h5>
                <h2>{{ $totalPayments }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card bg-warning">
            <div class="card-body">
                <h5>Total Revenue</h5>
                <h2>{{ $totalRevenue }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h5>Total Schedules</h5>
                <h2>{{ $totalSchedules }}</h2>
            </div>
        </div>
    </div>

</div>
<div class="container mt-5">

    <h1 class="mb-4">
        Admin Dashboard
    </h1>

    <div class="row">

        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <h5>Total Deceased</h5>

                    <h2>
                        {{ $totalDeceased }}
                    </h2>
                </div>
            </div>
        </div>


        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    <h5>Total Payments</h5>

                    <h2>
                        {{ $totalPayments }}
                    </h2>
                </div>
            </div>
        </div>


        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-dark shadow">
                <div class="card-body">
                    <h5>Available Rooms</h5>

                    <h2>
                        {{ $availableRooms }}
                    </h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
    <div class="card bg-success text-white p-3 shadow">
        <h5>Total Revenue</h5>
        <h2>{{ number_format($totalRevenue, 2) }}</h2>
    </div>
</div>


        <div class="col-md-3 mb-3">
            <div class="card bg-danger text-white shadow">
                <div class="card-body">
                    <h5>Pending Payments</h5>

                    <h2>
                        {{ $pendingPayments }}
                    </h2>
                </div>
            </div>
        </div>

    </div>

</div>

<div class="row">

    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-body">
                <h4>System Overview</h4>

                <p>
                    Registered Users:
                    <strong>{{ $totalUsers }}</strong>
                </p>

                <p>
                    Total Bodies Registered:
                    <strong>{{ $totalDeceased }}</strong>
                </p>

                <p>
                    Total Revenue:
                    <strong>{{ number_format($totalRevenue,2) }}</strong>
                </p>
            </div>
        </div>
    </div>

</div>

</div>


@endsection