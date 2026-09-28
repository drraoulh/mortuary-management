@extends('layouts.app')

@section('content')

@extends('layouts.app')

@section('content')

<div class="container">

<h1>Staff Dashboard</h1>

<div class="row">

<div class="col-md-4">
    <a href="/deceased" class="btn btn-primary w-100">
        Register Deceased
    </a>
</div>

<div class="col-md-4">
    <a href="/payments" class="btn btn-success w-100">
        Payments
    </a>
</div>

<div class="col-md-4">
    <a href="/schedule" class="btn btn-warning w-100">
        Schedule Pickup
    </a>
</div>

</div>

</div>

@endsection
<div class="container mt-4">
    <h1>STAFF DASHBOARD</h1>

<p>Welcome Staff Member</p>

<ul>
    <li>Register Deceased</li>
    <li>Update Deceased</li>
    <li>Search Deceased</li>
    <li>Record Payment</li>
    <li>Schedule Pickup</li>
    <li>View Notifications</li>
</ul>

    <div class="row">

        <div class="col-md-4">
            <a href="{{ route('deceased.index') }}" class="btn btn-primary w-100 mb-3">
                Register / Search Deceased
            </a>
        </div>

        <div class="col-md-3">
    <div class="card bg-success text-white p-3">
        <h5>My Payments</h5>

        <h2>
            {{ $myPayments->sum('amount') }}
        </h2>

        <small>FCFA paid by you</small>
    </div>
</div>

        <div class="col-md-4">
            <a href="{{ route('schedule.index') }}" class="btn btn-warning w-100 mb-3">
                Schedule Pickup
            </a>
        </div>

    </div>
</div>
@endsection