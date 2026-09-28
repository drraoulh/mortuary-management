@extends('layouts.app')

@section('content')

<div class="container">

<h2>Verify Deceased</h2>

<form method="POST" action="/verify">

@csrf

<input type="text"
       name="key"
       class="form-control mb-3"
       placeholder="Enter Identification Code">

<button class="btn btn-primary">
Verify
</button>

</form>

</div>

@endsection