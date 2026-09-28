@extends('layouts.app')

@section('title', 'New Schedule')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="page-hero">
            <span class="eyebrow">Schedule</span>
            <h1>Schedule a pickup</h1>
            <p>The pickup stays pending until a staff manager approves it.</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger border-0">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($deceaseds->isEmpty())
            <div class="surface-card empty-state">
                <i class="bi bi-person-plus"></i>
                <p class="mb-3">Register a deceased record before scheduling a pickup.</p>
                <a href="{{ route('deceased.create') }}" class="btn btn-accent">Add deceased</a>
            </div>
        @else
            <form action="{{ route('schedule.store') }}" method="POST" class="surface-card p-4 row g-3">
                @csrf

                <div class="col-12">
                    <label class="form-label fw-semibold">Deceased</label>
                    <select name="deceased_id" class="form-select" required>
                        @foreach($deceaseds as $deceased)
                            <option value="{{ $deceased->id }}" @selected(old('deceased_id') == $deceased->id)>
                                {{ $deceased->full_name }}
                                @if($deceased->identifier) ({{ $deceased->identifier }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Pickup date</label>
                    <input type="date" name="pickup_date" class="form-control" value="{{ old('pickup_date') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Pickup time</label>
                    <input type="time" name="pickup_time" class="form-control" value="{{ old('pickup_time') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Burial date (optional)</label>
                    <input type="date" name="burial_date" class="form-control" value="{{ old('burial_date') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Location (optional)</label>
                    <input type="text" name="location" class="form-control" value="{{ old('location') }}">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Notes</label>
                    <textarea name="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-accent">Save schedule</button>
                    <a href="{{ route('schedule.index') }}" class="btn btn-soft">Cancel</a>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection
