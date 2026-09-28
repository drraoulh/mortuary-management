@extends('layouts.app')

@section('title', 'Verify Deceased')

@section('content')

<div class="container pb-4" style="max-width: 640px;">

    <div class="page-header">
        <span class="eyebrow"><i class="bi bi-shield-check me-1"></i> Verification</span>
        <h1>Verify deceased information</h1>
        <p>Enter the verification key the mortuary gave the family at registration.</p>
    </div>

    @include('partials.flash')

    <div class="panel">
        <div class="panel-body p-4">
            <form action="{{ route('deceased.verify') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="key" class="form-label fw-semibold">Verification key</label>
                    <input type="text" name="key" id="key" class="form-control form-control-lg font-monospace"
                           value="{{ old('key') }}" placeholder="e.g. aB3dE9fGh1" required autofocus>
                </div>

                <button class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-search me-1"></i> Verify
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
