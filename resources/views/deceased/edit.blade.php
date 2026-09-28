@extends('layouts.app')

@section('title', 'Edit deceased')

@section('content')
<div class="page-hero">
    <h1>Edit {{ $deceased->full_name }}</h1>
    <p>{{ $deceased->identifier ?? 'No identifier' }}</p>
</div>

<div class="surface-card p-4" style="max-width: 860px;">
    @if($errors->any())
        <div class="alert alert-danger border-0">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('deceased.update', $deceased) }}" class="row g-3">
        @csrf
        @method('PUT')

        <div class="col-md-8">
            <label class="form-label fw-semibold">Full name</label>
            <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $deceased->full_name) }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold">Gender</label>
            <select name="gender" class="form-select" required>
                <option value="Male" @selected(old('gender', $deceased->gender) === 'Male')>Male</option>
                <option value="Female" @selected(old('gender', $deceased->gender) === 'Female')>Female</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Date of birth</label>
            <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $deceased->date_of_birth) }}">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Date of death</label>
            <input type="date" name="date_of_death" class="form-control" value="{{ old('date_of_death', $deceased->date_of_death) }}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Admission date</label>
            <input type="date" name="admission_date" class="form-control" value="{{ old('admission_date', $deceased->admission_date) }}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Release date</label>
            <input type="date" name="release_date" class="form-control" value="{{ old('release_date', $deceased->release_date) }}">
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Cause of death</label>
            <input type="text" name="cause_of_death" class="form-control" value="{{ old('cause_of_death', $deceased->cause_of_death) }}">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Room</label>
            <input type="text" name="room_name" class="form-control" value="{{ old('room_name', $deceased->room_name) }}" list="storage-rooms">
            <datalist id="storage-rooms">
                @foreach($rooms as $room)
                    <option value="{{ $room->room_number }}">{{ ucfirst($room->status) }} · capacity {{ $room->capacity }}</option>
                @endforeach
            </datalist>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Room type</label>
            <select name="room_type" class="form-select">
                <option value="normal" @selected(old('room_type', $deceased->room_type) === 'normal')>Normal</option>
                <option value="vip" @selected(old('room_type', $deceased->room_type) === 'vip')>VIP</option>
                <option value="vvip" @selected(old('room_type', $deceased->room_type) === 'vvip')>VVIP</option>
            </select>
        </div>

        <div class="col-12">
            <label class="form-label fw-semibold">Location address</label>
            <input type="text" name="location_address" class="form-control" value="{{ old('location_address', $deceased->location_address) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Latitude</label>
            <input type="text" name="latitude" class="form-control" value="{{ old('latitude', $deceased->latitude) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Longitude</label>
            <input type="text" name="longitude" class="form-control" value="{{ old('longitude', $deceased->longitude) }}">
        </div>

        <div class="col-12 d-flex gap-2">
            <button class="btn btn-accent" type="submit">Save</button>
            <a href="{{ route('deceased.show', $deceased) }}" class="btn btn-soft">Cancel</a>
        </div>
    </form>
</div>
@endsection
