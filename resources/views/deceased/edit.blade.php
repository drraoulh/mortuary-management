@extends('layouts.app')

@section('title', 'Edit ' . $deceased->full_name)

@section('content')

<div class="container pb-4" style="max-width: 900px;">

    <div class="page-header">
        <span class="eyebrow"><i class="bi bi-pencil me-1"></i> {{ $deceased->identifier ?? 'Deceased record' }}</span>
        <h1>Edit {{ $deceased->full_name }}</h1>
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

    <div class="panel">
        <div class="panel-body">
            <form method="POST" action="{{ route('deceased.update', $deceased) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full name</label>
                        <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $deceased->full_name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select" required>
                            @foreach(['Male', 'Female'] as $gender)
                                <option @selected(old('gender', $deceased->gender) === $gender)>{{ $gender }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date of birth</label>
                        <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $deceased->date_of_birth) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date of death</label>
                        <input type="date" name="date_of_death" class="form-control" value="{{ old('date_of_death', $deceased->date_of_death) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cause of death</label>
                        <input type="text" name="cause_of_death" class="form-control" value="{{ old('cause_of_death', $deceased->cause_of_death) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Admission date</label>
                        <input type="date" name="admission_date" class="form-control" value="{{ old('admission_date', $deceased->admission_date) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Release date</label>
                        <input type="date" name="release_date" class="form-control" value="{{ old('release_date', $deceased->release_date) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Room</label>
                        <input type="text" name="room_name" class="form-control" list="room-options" value="{{ old('room_name', $deceased->room_name) }}">
                        <datalist id="room-options">
                            @foreach($rooms as $room)
                                <option value="{{ $room->room_number }}">{{ ucfirst($room->status) }}</option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Room type</label>
                        <select name="room_type" class="form-select">
                            @foreach(['normal' => 'Normal', 'vip' => 'VIP', 'vvip' => 'VVIP'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('room_type', $deceased->room_type) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Location address</label>
                        <input type="text" name="location_address" class="form-control" value="{{ old('location_address', $deceased->location_address) }}">
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Save changes</button>
                    <a href="{{ route('deceased.show', $deceased) }}" class="btn btn-pink-soft rounded-pill px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
