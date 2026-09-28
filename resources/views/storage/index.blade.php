@extends('layouts.app')

@section('title', 'Storage Rooms')

@section('content')
@php($canManage = auth()->user()->canSupervise())

<div class="page-hero d-flex flex-wrap justify-content-between align-items-end gap-3">
    <div>
        <span class="eyebrow">Storage</span>
        <h1>Storage rooms</h1>
        <p>Cold rooms, their capacity and who is inside.</p>
    </div>
    @if($canManage)
        <a href="{{ route('storage.create') }}" class="btn btn-accent">
            <i class="bi bi-plus-circle me-1"></i> Add room
        </a>
    @endif
</div>

<div class="panel">
    @if($rooms->isEmpty())
        <div class="empty-state">
            <i class="bi bi-door-closed"></i>
            No storage rooms yet.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Room</th>
                        <th>Occupancy</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rooms as $room)
                        @php($percent = $room->capacity > 0 ? min(100, round($room->deceaseds_count / $room->capacity * 100)) : 0)
                        <tr>
                            <td class="ps-4 fw-semibold">Room {{ $room->room_number }}</td>
                            <td style="min-width: 180px;">
                                <div class="small mb-1">{{ $room->deceaseds_count }} / {{ $room->capacity }}</div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar" style="width: {{ $percent }}%"></div>
                                </div>
                            </td>
                            <td><span class="status-badge status-{{ $room->status }}">{{ $room->status }}</span></td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('storage.show', $room) }}" class="btn btn-sm btn-soft">Open</a>
                                    @if($canManage)
                                        <a href="{{ route('storage.edit', $room) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        <form action="{{ route('storage.destroy', $room) }}" method="POST"
                                              onsubmit="return confirm('Delete room {{ $room->room_number }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
