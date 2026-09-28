@extends('layouts.app')

@section('title', 'My Profile')

@section('content')

<div class="container pb-4" style="max-width: 820px;">

    <div class="page-header">
        <span class="eyebrow"><i class="bi bi-person-circle me-1"></i> {{ $user->roleLabel() }}</span>
        <h1>My profile</h1>
        <p>Manage your account details and password.</p>
    </div>

    <div class="panel mb-4">
        <div class="panel-body p-4">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <div class="panel mb-4">
        <div class="panel-body p-4">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <div class="panel border-danger-subtle">
        <div class="panel-body p-4">
            @include('profile.partials.delete-user-form')
        </div>
    </div>

</div>
@endsection
