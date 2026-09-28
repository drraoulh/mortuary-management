@extends('layouts.app')

@section('title', 'Room ' . $storage->room_number)

@section('content')
<div class="page-hero d-flex flex-wrap justify-content-between align-items-end gap-3">
    <div>
        <span class="eyebrow">Storage</span>
        <h1>Room {{ $storage->room_number }}</h1>
        <p>
            {{ $storage->deceaseds->count() }} / {{ $storage->capacity }} occupied
            &middot; <span class="status-badge status-{{ $storage->status }}">{{ $storage->status }}</span>
        </p>
    </div>
    <div class="d-flex gap-2">
        @if(auth()->user()->canSupervise())
            <a href="{{ route('storage.edit', $storage) }}" class="btn btn-soft">Edit</a>
        @endif
        <a href="{{ route('storage.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title"><i class="bi bi-people"></i> Occupants</h2>
    </div>
    <div class="panel-body">
        @forelse($storage->deceaseds as $deceased)
            <a href="{{ route('deceased.show', $deceased) }}" class="list-row text-decoration-none text-reset">
                <div class="lr-icon"><i class="bi bi-person"></i></div>
                <div class="lr-main">
                    <div class="lr-title">{{ $deceased->full_name }}</div>
                    <div class="lr-sub">{{ $deceased->identifier ?? 'No identifier' }} &middot; admitted {{ $deceased->admission_date }}</div>
                </div>
            </a>
        @empty
            <div class="empty-state"><i class="bi bi-door-open"></i>This room is empty.</div>
        @endforelse
    </div>
</div>
@endsection
