@extends('layouts.app')

@section('title', $deceased->full_name)

@section('content')
<div class="page-hero d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
        <h1>{{ $deceased->full_name }}</h1>
        <p>{{ $deceased->identifier ?? 'No identifier' }} · {{ $deceased->gender }}</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('ai.index') }}?deceased_id={{ $deceased->id }}" class="btn btn-accent">AI tools</a>
        <a href="{{ route('deceased.edit', $deceased) }}" class="btn btn-soft">Edit</a>
        <a href="{{ route('deceased.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="surface-card p-4">
            <div class="row g-3">
                <div class="col-md-6"><strong>Date of birth</strong><div>{{ $deceased->date_of_birth ?? '—' }}</div></div>
                <div class="col-md-6"><strong>Date of death</strong><div>{{ $deceased->date_of_death ?? '—' }}</div></div>
                <div class="col-md-6"><strong>Admission</strong><div>{{ $deceased->admission_date ?? '—' }}</div></div>
                <div class="col-md-6"><strong>Cause of death</strong><div>{{ $deceased->cause_of_death ?? '—' }}</div></div>
                <div class="col-md-6"><strong>Room</strong><div>{{ $deceased->room_name ?? '—' }} ({{ $deceased->room_type ?? '—' }})</div></div>
                <div class="col-md-6"><strong>Price</strong><div>{{ $deceased->price ? number_format((float)$deceased->price, 0).' XAF' : '—' }}</div></div>
                <div class="col-12"><strong>Location</strong><div>{{ $deceased->location_address ?? '—' }}</div></div>
                <div class="col-md-6"><strong>Latitude</strong><div>{{ $deceased->latitude ?? '—' }}</div></div>
                <div class="col-md-6"><strong>Longitude</strong><div>{{ $deceased->longitude ?? '—' }}</div></div>
                <div class="col-12"><strong>Security key</strong><div><code>{{ $deceased->security_key ?? '—' }}</code></div></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="surface-card p-4">
            @if($deceased->photo)
                <img src="{{ asset('storage/'.$deceased->photo) }}" alt="Photo" class="w-100 rounded-4 mb-3" style="object-fit:cover; max-height:280px;">
            @else
                <div class="text-muted mb-3">No photo uploaded yet.</div>
            @endif
            <a href="{{ route('faire-part.create') }}" class="btn btn-soft w-100 mb-2">Generate faire-part</a>
            <a href="{{ route('payments.create') }}" class="btn btn-outline-secondary w-100">Create payment</a>
        </div>
    </div>
</div>
@endsection
