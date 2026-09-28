@extends('layouts.app')

@section('title', 'Deceased Records')

@section('content')

<div class="container pb-4">

    <div class="page-header d-flex flex-wrap justify-content-between align-items-end gap-3">
        <div>
            <span class="eyebrow"><i class="bi bi-person-vcard me-1"></i> Records</span>
            <h1>Deceased records</h1>
            <p>{{ $deceaseds->count() }} record(s){{ request('search') ? ' matching "' . request('search') . '"' : '' }}.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('faire-part.create') }}" class="btn btn-pink-soft rounded-pill px-3">
                <i class="bi bi-envelope-paper-heart me-1"></i> Faire-part
            </a>
            <a href="{{ route('deceased.create') }}" class="btn btn-primary rounded-pill px-3">
                <i class="bi bi-plus-lg me-1"></i> Register new body
            </a>
        </div>
    </div>

    @include('partials.flash')

    <form method="GET" class="mb-3">
        <div class="input-group">
            <span class="input-group-text bg-white"><i class="bi bi-search text-pink"></i></span>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name">
            @if(request('search'))
                <a href="{{ route('deceased.index') }}" class="btn btn-pink-soft">Clear</a>
            @endif
            <button class="btn btn-primary">Search</button>
        </div>
    </form>

    <div class="panel">
        <div class="panel-body p-0">
            @if($deceaseds->isEmpty())
                <div class="empty-state"><i class="bi bi-person-vcard"></i>No records found.</div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Name</th>
                                <th>Gender</th>
                                <th>Date of death</th>
                                <th>Admitted</th>
                                <th>Room</th>
                                <th class="text-end">Price</th>
                                <th class="pe-4"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deceaseds as $deceased)
                                <tr>
                                    <td class="ps-4">
                                        <a href="{{ route('deceased.show', $deceased) }}" class="fw-semibold text-reset text-decoration-none">
                                            {{ $deceased->full_name }}
                                        </a>
                                        <div class="small text-muted">{{ $deceased->identifier ?? '—' }}</div>
                                    </td>
                                    <td class="small">{{ $deceased->gender }}</td>
                                    <td class="small">{{ $deceased->date_of_death }}</td>
                                    <td class="small">{{ $deceased->admission_date }}</td>
                                    <td class="small">
                                        {{ $deceased->room_name ?: '—' }}
                                        @if($deceased->room_type)
                                            <span class="badge rounded-pill bg-pink-soft ms-1">{{ strtoupper($deceased->room_type) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end small fw-semibold">
                                        {{ $deceased->price ? number_format($deceased->price, 0, ',', ' ') : '—' }}
                                    </td>
                                    <td class="pe-4 text-end text-nowrap">
                                        <a href="{{ route('deceased.edit', $deceased) }}" class="btn btn-sm btn-pink-soft rounded-pill px-3">Edit</a>
                                        @if(auth()->user()->canSupervise())
                                            <form method="POST" action="{{ route('deceased.destroy', $deceased) }}" class="d-inline"
                                                  onsubmit="return confirm('Delete this record permanently?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger rounded-pill px-3">Delete</button>
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
