@extends('layouts.app')

@section('title', 'Verify a Deceased')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="page-hero text-center">
            <span class="eyebrow">Verification</span>
            <h1>Verify deceased information</h1>
            <p>Enter the verification key given to the family by the mortuary.</p>
        </div>

        <form action="{{ route('deceased.verify') }}" method="POST" class="surface-card p-4">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold" for="key">Verification key</label>
                <input type="text" id="key" name="key" class="form-control form-control-lg"
                       value="{{ old('key') }}" placeholder="e.g. aB3xK9pQ2z" required>
                @error('key')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            @if(auth()->user()->isClient())
                <p class="text-muted small">
                    Once verified, the deceased appears in your space so you can pay fees and create a faire-part.
                </p>
            @endif

            <button class="btn btn-accent w-100">
                <i class="bi bi-shield-check me-1"></i> Verify
            </button>
        </form>
    </div>
</div>
@endsection
