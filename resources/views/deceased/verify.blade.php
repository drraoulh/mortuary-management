@extends('layouts.app')

@section('content')

<div class="container">

    <h2>Verify Deceased Information</h2>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="/verify" method="POST">
        @csrf

        <div class="mb-3">
            <label>Identification Code</label>

            <input type="text"
                   name="key"
                   class="form-control"
                   placeholder="Enter Identifier">
        </div>

        <button class="btn btn-primary">
            Verify
        </button>

    </form>

</div>

@endsection