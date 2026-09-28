@extends('layouts.app')

@section('title', 'Register New Body')

@section('content')

<div class="container pb-4" style="max-width: 900px;">

    <div class="page-header">
        <span class="eyebrow"><i class="bi bi-person-plus me-1"></i> Admission</span>
        <h1>Register new body</h1>
        <p>An identifier and a verification key for the family are generated automatically.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('deceased.store') }}" method="POST">
        @csrf

        <div class="panel mb-4">
            <div class="panel-header">
                <h2 class="panel-title"><i class="bi bi-person"></i> Identity</h2>
            </div>
            <div class="panel-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Full name</label>
                        <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Gender</label>
                        <select name="gender" class="form-select" required>
                            <option value="">Select gender</option>
                            @foreach(['Male', 'Female'] as $gender)
                                <option @selected(old('gender') === $gender)>{{ $gender }}</option>
                            @endforeach
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
                        <label class="form-label fw-semibold">Cause of death</label>
                        <input type="text" name="cause_of_death" class="form-control" value="{{ old('cause_of_death') }}" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel mb-4">
            <div class="panel-header">
                <h2 class="panel-title"><i class="bi bi-door-closed"></i> Stay at the mortuary</h2>
            </div>
            <div class="panel-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Admission date</label>
                        <input type="date" name="admission_date" class="form-control"
                               value="{{ old('admission_date', now()->toDateString()) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Expected release date</label>
                        <input type="date" name="release_date" class="form-control" value="{{ old('release_date') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Room</label>
                        <input type="text" name="room_name" class="form-control" list="room-options"
                               value="{{ old('room_name') }}" placeholder="Choose or type a room">
                        <datalist id="room-options">
                            @foreach($rooms as $room)
                                <option value="{{ $room->room_number }}">{{ ucfirst($room->status) }}</option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Room type</label>
                        <select name="room_type" class="form-select">
                            @foreach(['normal' => 'Normal — 10 000 FCFA', 'vip' => 'VIP — 25 000 FCFA', 'vvip' => 'VVIP — 50 000 FCFA'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('room_type', 'normal') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel mb-4">
            <div class="panel-header">
                <h2 class="panel-title"><i class="bi bi-geo-alt"></i> Location (optional)</h2>
            </div>
            <div class="panel-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label for="location_address" class="form-label fw-semibold">Location address</label>
                        <input type="text" name="location_address" id="location_address" class="form-control"
                               value="{{ old('location_address') }}" placeholder="Enter location address">
                    </div>
                    <div class="col-md-6">
                        <label for="latitude" class="form-label fw-semibold">Latitude</label>
                        <input type="text" name="latitude" id="latitude" class="form-control"
                               value="{{ old('latitude') }}" placeholder="Example: 3.8480">
                    </div>
                    <div class="col-md-6">
                        <label for="longitude" class="form-label fw-semibold">Longitude</label>
                        <input type="text" name="longitude" id="longitude" class="form-control"
                               value="{{ old('longitude') }}" placeholder="Example: 11.5021">
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary rounded-pill px-4">Save record</button>
            <a href="{{ route('deceased.index') }}" class="btn btn-pink-soft rounded-pill px-4">Back</a>
        </div>
    </form>

</div>
@endsection
