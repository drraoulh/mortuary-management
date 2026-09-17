@extends('layouts.app')

@section('title', 'Register deceased')

@section('content')
<div class="page-hero">
    <h1>Register deceased</h1>
    <p>Capture the case details used by payments, schedule and AI tools.</p>
</div>

<div class="surface-card p-4 p-md-5" style="max-width: 860px;">
    @if($errors->any())
        <div class="alert alert-danger border-0">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('deceased.store') }}" method="POST" class="row g-3">
        @csrf

        <div class="col-md-8">
            <label class="form-label fw-semibold">Full name</label>
            <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold">Gender</label>
            <select name="gender" class="form-select" required>
                <option value="">Select</option>
                <option value="Male" @selected(old('gender') === 'Male')>Male</option>
                <option value="Female" @selected(old('gender') === 'Female')>Female</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Date of birth</label>
            <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold">Date of death</label>
            <input type="date" name="date_of_death" class="form-control" value="{{ old('date_of_death') }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold">Admission date</label>
            <input type="date" name="admission_date" class="form-control" value="{{ old('admission_date', now()->toDateString()) }}" required>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Cause of death</label>
            <input type="text" name="cause_of_death" class="form-control" value="{{ old('cause_of_death') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Room name</label>
            <input type="text" name="room_name" class="form-control" value="{{ old('room_name') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Room type</label>
            <select name="room_type" class="form-select">
                <option value="normal" @selected(old('room_type', 'normal') === 'normal')>Normal</option>
                <option value="vip" @selected(old('room_type') === 'vip')>VIP</option>
                <option value="vvip" @selected(old('room_type') === 'vvip')>VVIP</option>
            </select>
        </div>

        <div class="col-12">
            <label class="form-label fw-semibold">Location address</label>
            <input type="text" name="location_address" class="form-control" value="{{ old('location_address') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Latitude</label>
            <input type="text" name="latitude" class="form-control" value="{{ old('latitude') }}" placeholder="3.8480">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Longitude</label>
            <input type="text" name="longitude" class="form-control" value="{{ old('longitude') }}" placeholder="11.5021">
        </div>

        <div class="col-12 d-flex gap-2 mt-2">
            <button type="submit" class="btn btn-accent">Save record</button>
            <a href="{{ route('deceased.index') }}" class="btn btn-soft">Back</a>
        </div>
    </form>
</div>
@endsection
