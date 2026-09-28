@extends('layouts.app')

@section('title', $deceased->full_name)

@section('content')

<div class="container pb-4" style="max-width: 900px;">

    <div class="page-header d-flex flex-wrap justify-content-between align-items-end gap-3">
        <div>
            <span class="eyebrow"><i class="bi bi-person-vcard me-1"></i> {{ $deceased->identifier ?? 'Deceased record' }}</span>
            <h1>{{ $deceased->full_name }}</h1>
            <p>Registered {{ $deceased->created_at?->format('d M Y') }}</p>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()->isClient())
                <a href="{{ route('dashboard') }}" class="btn btn-pink-soft rounded-pill px-3">My Space</a>
                <a href="{{ route('payments.create', ['deceased_id' => $deceased->id]) }}" class="btn btn-primary rounded-pill px-3">
                    <i class="bi bi-phone me-1"></i> Make a payment
                </a>
            @else
                <a href="{{ route('deceased.index') }}" class="btn btn-pink-soft rounded-pill px-3">Back</a>
                <a href="{{ route('deceased.edit', $deceased) }}" class="btn btn-primary rounded-pill px-3">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
            @endif
        </div>
    </div>

    @include('partials.flash')

    @if(! auth()->user()->isClient() && $deceased->security_key)
        <div class="alert bg-pink-soft rounded-4 d-flex align-items-center gap-2">
            <i class="bi bi-key"></i>
            <span>Family verification key: <strong class="font-monospace">{{ $deceased->security_key }}</strong></span>
        </div>
    @endif

    <div class="panel">
        <div class="panel-body">
            <div class="row g-4">
                @foreach([
                    'Gender' => $deceased->gender,
                    'Date of birth' => $deceased->date_of_birth,
                    'Date of death' => $deceased->date_of_death,
                    'Cause of death' => $deceased->cause_of_death,
                    'Admission date' => $deceased->admission_date,
                    'Release date' => $deceased->release_date,
                    'Room' => $deceased->room_name,
                    'Room type' => $deceased->room_type ? strtoupper($deceased->room_type) : null,
                    'Price' => $deceased->price ? number_format($deceased->price, 0, ',', ' ') . ' FCFA' : null,
                    'Location' => $deceased->location_address,
                ] as $label => $value)
                    <div class="col-sm-6">
                        <div class="small text-muted">{{ $label }}</div>
                        <div class="fw-semibold">{{ $value ?: '—' }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endsection
