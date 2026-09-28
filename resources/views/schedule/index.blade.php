@extends('layouts.app')

@section('title', 'Schedule')

@section('content')

<div class="container pb-4">

    <div class="page-header d-flex flex-wrap justify-content-between align-items-end gap-3">
        <div>
            <span class="eyebrow"><i class="bi bi-calendar-event me-1"></i> Schedule</span>
            <h1>Pickup schedule</h1>
            <p>Planned releases and burials.</p>
        </div>
        <a href="{{ route('schedule.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-calendar-plus me-1"></i> New pickup
        </a>
    </div>

    @include('partials.flash')

    <div class="panel">
        <div class="panel-body p-0">
            @if($schedules->isEmpty())
                <div class="empty-state"><i class="bi bi-calendar2"></i>No pickups scheduled yet.</div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Deceased</th>
                                <th>Pickup</th>
                                <th>Burial</th>
                                <th>Destination</th>
                                <th>Notes</th>
                                <th>Status</th>
                                <th class="pe-4"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schedules as $schedule)
                                <tr>
                                    <td class="ps-4 fw-semibold">{{ $schedule->deceased->full_name ?? '—' }}</td>
                                    <td class="small">
                                        {{ $schedule->pickup_date }}
                                        @if($schedule->pickup_time) &middot; {{ substr($schedule->pickup_time, 0, 5) }} @endif
                                    </td>
                                    <td class="small">{{ $schedule->burial_date ?? '—' }}</td>
                                    <td class="small">{{ $schedule->location ?: '—' }}</td>
                                    <td class="small text-muted">{{ $schedule->notes ?: '—' }}</td>
                                    <td>
                                        <span class="status-badge status-default status-{{ $schedule->status ?? 'pending' }}">
                                            {{ $schedule->status ?? 'pending' }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        @if(($schedule->status ?? 'pending') === 'pending' && auth()->user()->canSupervise())
                                            <form action="{{ route('schedule.confirm', $schedule->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-primary rounded-pill px-3">Approve</button>
                                            </form>
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
