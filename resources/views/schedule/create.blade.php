@extends('layouts.app')

@section('title', 'New Pickup')

@section('content')

<div class="container pb-4" style="max-width: 760px;">

    <div class="page-header">
        <span class="eyebrow"><i class="bi bi-calendar-plus me-1"></i> Schedule</span>
        <h1>Schedule a pickup</h1>
        <p>A manager approves the pickup before it is confirmed.</p>
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
        <div class="panel-body p-4">
            <form action="{{ route('schedule.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-12">
                        <label for="deceased_id" class="form-label fw-semibold">Deceased</label>
                        <select name="deceased_id" id="deceased_id" class="form-select" required>
                            <option value="">Select the deceased</option>
                            @foreach($deceaseds as $deceased)
                                <option value="{{ $deceased->id }}" @selected(old('deceased_id') == $deceased->id)>
                                    {{ $deceased->full_name }}
                                    @if($deceased->identifier) ({{ $deceased->identifier }}) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="pickup_date" class="form-label fw-semibold">Pickup date</label>
                        <input type="date" name="pickup_date" id="pickup_date" class="form-control"
                               value="{{ old('pickup_date') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="pickup_time" class="form-label fw-semibold">Pickup time</label>
                        <input type="time" name="pickup_time" id="pickup_time" class="form-control"
                               value="{{ old('pickup_time') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="burial_date" class="form-label fw-semibold">Burial date</label>
                        <input type="date" name="burial_date" id="burial_date" class="form-control"
                               value="{{ old('burial_date') }}">
                    </div>

                    <div class="col-md-6">
                        <label for="location" class="form-label fw-semibold">Destination</label>
                        <input type="text" name="location" id="location" class="form-control"
                               value="{{ old('location') }}" placeholder="Church, village, cemetery…">
                    </div>

                    <div class="col-12">
                        <label for="notes" class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Save pickup</button>
                    <a href="{{ route('schedule.index') }}" class="btn btn-pink-soft rounded-pill px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
