@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1">Assistant IA</h2>
            <p class="text-muted mb-0">
                Faire-part, condoléances, SMS famille et résumé de dossier.
            </p>
        </div>
        <a href="{{ route('faire-part.create') }}" class="btn btn-outline-primary">
            Faire-part dédié
        </a>
    </div>

    @if(!$hfConfigured)
        <div class="alert alert-warning">
            <strong>HF_TOKEN</strong> est présent mais doit autoriser
            <em>Make calls to Inference Providers</em> sur Hugging Face.
            En attendant, l’application utilise un générateur local digne et sûr.
        </div>
    @else
        <div class="alert alert-success">
            Hugging Face configuré (modèle: <code>{{ config('services.huggingface.model') }}</code>).
            Si l’API refuse l’appel, un fallback local est utilisé automatiquement.
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    @if($deceaseds->isEmpty())
                        <div class="alert alert-info mb-0">
                            Enregistrez d’abord un défunt.
                            <a href="{{ route('deceased.create') }}">Ajouter</a>
                        </div>
                    @else
                        <form method="POST" action="{{ route('ai.generate') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Défunt</label>
                                <select name="deceased_id" class="form-select" required>
                                    @foreach($deceaseds as $deceased)
                                        <option value="{{ $deceased->id }}" @selected(old('deceased_id') == $deceased->id)>
                                            {{ $deceased->full_name }}
                                            @if($deceased->identifier) ({{ $deceased->identifier }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Fonctionnalité</label>
                                <select name="feature" id="feature" class="form-select" required>
                                    <option value="faire_part" @selected(old('feature') === 'faire_part')>Faire-part funéraire</option>
                                    <option value="condolences" @selected(old('feature') === 'condolences')>Message de condoléances</option>
                                    <option value="sms" @selected(old('feature') === 'sms')>SMS à la famille</option>
                                    <option value="summary" @selected(old('feature') === 'summary')>Résumé du dossier</option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Langue</label>
                                    <select name="language" class="form-select">
                                        <option value="fr" @selected(old('language', 'fr') === 'fr')>Français</option>
                                        <option value="en" @selected(old('language') === 'en')>English</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3" id="tone-wrap">
                                    <label class="form-label fw-semibold">Ton (condoléances)</label>
                                    <select name="tone" class="form-select">
                                        <option value="formal">Formel</option>
                                        <option value="warm">Chaleureux</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3" id="sms-wrap" style="display:none;">
                                <label class="form-label fw-semibold">Type de SMS</label>
                                <select name="sms_purpose" class="form-select">
                                    <option value="pickup">Levée / récupération</option>
                                    <option value="payment">Rappel de paiement</option>
                                    <option value="schedule">Planning cérémonie</option>
                                </select>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" value="1" id="save_notice" name="save_notice">
                                <label class="form-check-label" for="save_notice">
                                    Enregistrer le texte (faire-part / condoléances)
                                </label>
                            </div>

                            <button type="submit" class="btn btn-success btn-lg">
                                Générer avec l’IA
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold">Derniers textes enregistrés</h5>
                    @forelse($notices as $notice)
                        <div class="border-bottom py-3">
                            <div class="fw-semibold">
                                {{ $notice->deceased->full_name ?? 'N/A' }}
                            </div>
                            <div class="small text-muted mb-1">
                                {{ $notice->theme }} · {{ $notice->language }} · {{ $notice->created_at?->diffForHumans() }}
                            </div>
                            <div class="small" style="white-space: pre-line;">
                                {{ \Illuminate\Support\Str::limit($notice->announcement, 160) }}
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Aucun texte enregistré pour le moment.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const feature = document.getElementById('feature');
    const smsWrap = document.getElementById('sms-wrap');
    const toneWrap = document.getElementById('tone-wrap');

    function refresh() {
        const value = feature.value;
        smsWrap.style.display = value === 'sms' ? 'block' : 'none';
        toneWrap.style.display = value === 'condolences' ? 'block' : 'none';
    }

    feature.addEventListener('change', refresh);
    refresh();
});
</script>
@endsection
