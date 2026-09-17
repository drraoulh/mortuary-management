@extends('layouts.app')

@section('title', 'Deceased')

@section('content')
<div class="page-hero d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
        <h1>Deceased records</h1>
        <p>Register, search and open AI tools for each case.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('deceased.create') }}" class="btn btn-accent">Register</a>
        <a href="{{ route('ai.index') }}" class="btn btn-soft">AI Assistant</a>
        <a href="{{ route('faire-part.create') }}" class="btn btn-soft">Faire-part</a>
    </div>
</div>

<div class="surface-card p-3 p-md-4 mb-3">
    <form method="GET" class="row g-2 align-items-center">
        <div class="col-md-8">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name">
        </div>
        <div class="col-md-4 d-grid">
            <button class="btn btn-outline-secondary" type="submit">Search</button>
        </div>
    </form>
</div>

<div class="surface-card p-3 p-md-4">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>ID</th>
                    <th>Gender</th>
                    <th>Death</th>
                    <th>Room</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($deceaseds as $deceased)
                    <tr>
                        <td class="fw-semibold">{{ $deceased->full_name }}</td>
                        <td>{{ $deceased->identifier ?? '—' }}</td>
                        <td>{{ $deceased->gender }}</td>
                        <td>{{ $deceased->date_of_death }}</td>
                        <td>{{ $deceased->room_name ?? '—' }}</td>
                        <td>{{ $deceased->room_type ?? '—' }}</td>
                        <td>{{ $deceased->price ? number_format((float) $deceased->price, 0) . ' XAF' : '—' }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-soft" href="{{ route('deceased.show', $deceased) }}">Open</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-muted text-center py-4">No deceased records yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
