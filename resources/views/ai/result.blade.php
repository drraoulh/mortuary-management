@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1">{{ $title }}</h2>
            <p class="text-muted mb-0">
                {{ $deceased->full_name }}
                · {{ strtoupper($language) }}
                · {{ $provider === 'huggingface' ? 'Hugging Face' : 'Générateur local' }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary">Imprimer</button>
            <a href="{{ route('ai.index') }}" class="btn btn-outline-secondary">Retour IA</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4 p-md-5">
            <div style="max-width: 820px; margin: auto; font-family: Georgia, serif; line-height: 1.8; white-space: pre-line;">
                {{ $text }}
            </div>
        </div>
    </div>
</div>
@endsection
