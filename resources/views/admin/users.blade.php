@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="page-hero">
    <span class="eyebrow"><i class="bi bi-shield-lock me-1"></i> Administrator</span>
    <h1>Users &amp; roles</h1>
    <p>Choose who is a family, mortuary staff, a staff manager or an administrator.</p>
</div>

<div class="panel">
    <div class="panel-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">User</th>
                        <th class="text-center">Bodies</th>
                        <th class="text-center">Payments</th>
                        <th>Joined</th>
                        <th class="pe-4" style="min-width: 260px;">Role</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $member)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold">
                                    {{ $member->name }}
                                    @if($member->is(auth()->user()))
                                        <span class="badge rounded-pill bg-pink-soft ms-1">You</span>
                                    @endif
                                </div>
                                <div class="small text-muted">{{ $member->email }}</div>
                            </td>
                            <td class="text-center">{{ $member->deceaseds_count }}</td>
                            <td class="text-center">{{ $member->payments_count }}</td>
                            <td class="small text-muted">{{ $member->created_at?->format('d M Y') }}</td>
                            <td class="pe-4">
                                <form method="POST" action="{{ route('admin.users.role', $member) }}" class="d-flex gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" class="form-select form-select-sm">
                                        @foreach(\App\Models\User::ROLES as $value => $label)
                                            <option value="{{ $value }}" @selected($member->role === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-accent rounded-pill px-3">Save</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
