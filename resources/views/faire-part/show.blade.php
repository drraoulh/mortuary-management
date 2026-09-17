@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold">Faire-part funéraire</h2>
            <p class="text-muted mb-0">
                {{ $deceased->full_name }}
                · {{ strtoupper($language ?? 'fr') }}
                · {{ ($provider ?? 'local') === 'huggingface' ? 'Hugging Face' : 'Générateur local' }}
            </p>
        </div>
        <button onclick="window.print()" class="btn btn-primary">Imprimer</button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-5">
            @if(!empty($photoUrl))
                <div class="text-center mb-4">
                    <img src="{{ $photoUrl }}" alt="Photo" style="max-height: 220px; border-radius: 8px;">
                </div>
            @endif

            <div style="max-width: 800px; margin: auto; font-family: Georgia, serif; line-height: 1.8; white-space: pre-line;">
                {{ $fairePart }}
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <a href="{{ route('faire-part.create') }}" class="btn btn-secondary">Nouveau</a>
        <a href="{{ route('ai.index') }}" class="btn btn-outline-primary">Assistant IA</a>
    </div>
</div>
@endsection
