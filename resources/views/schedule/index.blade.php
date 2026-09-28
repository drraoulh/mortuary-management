@extends('layouts.app')

@section('title', 'Schedule')

@section('content')
<div class="page-hero d-flex flex-wrap justify-content-between align-items-end gap-3">
    <div>
        <span class="eyebrow">Schedule</span>
        <h1>Pickups &amp; burials</h1>
        <p>Planned releases; a staff manager approves each pickup.</p>
    </div>
    <a href="{{ route('schedule.create') }}" class="btn btn-accent">
        <i class="bi bi-calendar-plus me-1"></i> New schedule
    </a>
</div>

<div class="panel">
    @if($schedules->isEmpty())
        <div class="empty-state">
            <i class="bi bi-calendar2"></i>
            No pickups scheduled yet.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Deceased</th>
                        <th>Pickup</th>
                        <th>Burial</th>
                        <th>Location</th>
                        <th>Notes</th>
                        <th>Status</th>
                        <th class="text-end pe-4"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schedules as $schedule)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $schedule->deceased->full_name ?? 'Unknown' }}</td>
                            <td>
                                {{ \Illuminate\Support\Carbon::parse($schedule->pickup_date)->format('d/m/Y') }}
                                @if($schedule->pickup_time)
                                    <span class="text-muted small">{{ substr($schedule->pickup_time, 0, 5) }}</span>
                                @endif
                            </td>
                            <td>{{ $schedule->burial_date ? \Illuminate\Support\Carbon::parse($schedule->burial_date)->format('d/m/Y') : '—' }}</td>
                            <td>{{ $schedule->location ?? '—' }}</td>
                            <td class="small text-muted">{{ $schedule->notes ?? '—' }}</td>
                            <td>
                                <span class="status-badge status-{{ $schedule->status ?? 'pending' }}">{{ $schedule->status ?? 'pending' }}</span>
                            </td>
                            <td class="text-end pe-4">
                                @if($schedule->status === 'pending' && auth()->user()->canSupervise())
                                    <form action="{{ route('schedule.confirm', $schedule->id) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm btn-accent rounded-pill">Approve</button>
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
@endsection
