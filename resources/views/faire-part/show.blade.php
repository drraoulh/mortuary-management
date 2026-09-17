@extends('layouts.app')

@section('title', 'Faire-part')

@section('content')
<div class="page-hero d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h1>{{ ($language ?? 'fr') === 'en' ? 'Funeral announcement' : 'Faire-part funéraire' }}</h1>
        <p>
            {{ $deceased->full_name }}
            · {{ strtoupper($language ?? 'fr') }}
            · {{ ($provider ?? 'local') === 'huggingface' ? 'Hugging Face' : 'Local' }}
        </p>
    </div>
    <div class="d-flex gap-2 no-print">
        <button onclick="window.print()" class="btn btn-accent">Print</button>
        <a href="{{ route('faire-part.create') }}" class="btn btn-soft">New</a>
    </div>
</div>

<div class="surface-card p-4 p-md-5">
    @if(!empty($photoUrl))
        <div class="text-center mb-4">
            <img src="{{ $photoUrl }}" alt="Photo" style="max-height: 240px; border-radius: 16px; border: 1px solid #d7e0ea;">
        </div>
    @endif

    <div style="max-width: 800px; margin: auto; font-family: 'Source Serif 4', Georgia, serif; line-height: 1.85; white-space: pre-line; font-size: 1.05rem;">
        {{ $fairePart }}
    </div>
</div>
@endsection
