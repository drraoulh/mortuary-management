@extends('layouts.app')

@section('title', 'Storage Rooms')

@section('content')

<div class="container pb-4">

    <div class="page-header d-flex flex-wrap justify-content-between align-items-end gap-3">
        <div>
            <span class="eyebrow"><i class="bi bi-door-closed me-1"></i> Storage</span>
            <h1>Storage rooms</h1>
            <p>{{ $rooms->where('status', 'available')->count() }} of {{ $rooms->count() }} rooms available.</p>
        </div>
        @if(auth()->user()->canSupervise())
            <a href="{{ route('storage.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> Add room
            </a>
        @endif
    </div>

    @include('partials.flash')

    <div class="panel">
        <div class="panel-body p-0">
            @if($rooms->isEmpty())
                <div class="empty-state"><i class="bi bi-door-closed"></i>No storage rooms yet.</div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Room</th>
                                <th class="text-center">Capacity</th>
                                <th>Occupants</th>
                                <th>Status</th>
                                <th class="pe-4"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rooms as $room)
                                <tr>
                                    <td class="ps-4 fw-semibold">
                                        <a href="{{ route('storage.show', $room) }}" class="text-reset text-decoration-none">
                                            Room {{ $room->room_number }}
                                        </a>
                                    </td>
                                    <td class="text-center">{{ $room->deceaseds->count() }} / {{ $room->capacity }}</td>
                                    <td class="small text-muted">
                                        {{ $room->deceaseds->pluck('full_name')->join(', ') ?: '—' }}
                                    </td>
                                    <td>
                                        <span class="status-badge status-default status-{{ $room->status }}">{{ $room->status }}</span>
                                    </td>
                                    <td class="pe-4 text-end text-nowrap">
                                        @if(auth()->user()->canSupervise())
                                            <a href="{{ route('storage.edit', $room) }}" class="btn btn-sm btn-pink-soft rounded-pill px-3">Edit</a>
                                            <form method="POST" action="{{ route('storage.destroy', $room) }}" class="d-inline"
                                                  onsubmit="return confirm('Delete this room?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger rounded-pill px-3">Delete</button>
                                            </form>
                                        @else
                                            <a href="{{ route('storage.show', $room) }}" class="btn btn-sm btn-pink-soft rounded-pill px-3">View</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
