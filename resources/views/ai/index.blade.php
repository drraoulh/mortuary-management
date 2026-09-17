@extends('layouts.app')

@section('title', 'AI Assistant')

@section('content')
<div class="page-hero d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
        <h1>AI Assistant / Assistant IA</h1>
        <p>Choose English or French. Add a photo for faire-part and condolences.</p>
    </div>
    <a href="{{ route('faire-part.create') }}" class="btn btn-soft no-print">Dedicated faire-part</a>
</div>

@if(!$hfConfigured)
    <div class="alert alert-warning border-0">
        Hugging Face token needs <strong>Inference Providers</strong> permission.
        Until then, a dignified local generator is used automatically.
    </div>
@else
    <div class="alert alert-success border-0">
        Hugging Face ready · model <code>{{ config('services.huggingface.model') }}</code>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger border-0">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-7">
        <div class="surface-card p-4">
            @if($deceaseds->isEmpty())
                <div class="alert alert-info mb-0">
                    Register a deceased record first.
                    <a href="{{ route('deceased.create') }}">Add deceased</a>
                </div>
            @else
                <form method="POST" action="{{ route('ai.generate') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deceased / Défunt</label>
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
                        <label class="form-label fw-semibold d-block">Language / Langue</label>
                        <div class="btn-group lang-toggle" role="group">
                            <input type="radio" class="btn-check" name="language" id="lang-fr" value="fr" autocomplete="off" @checked(old('language', 'fr') === 'fr')>
                            <label class="btn btn-outline-success" for="lang-fr">FR</label>

                            <input type="radio" class="btn-check" name="language" id="lang-en" value="en" autocomplete="off" @checked(old('language') === 'en')>
                            <label class="btn btn-outline-success" for="lang-en">EN</label>
                        </div>
                        <div class="form-text">The AI answer will be generated only in the selected language.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Feature / Fonctionnalité</label>
                        <select name="feature" id="feature" class="form-select" required>
                            <option value="faire_part" @selected(old('feature') === 'faire_part')>Faire-part / Funeral announcement</option>
                            <option value="condolences" @selected(old('feature') === 'condolences')>Condolences / Condoléances</option>
                            <option value="sms" @selected(old('feature') === 'sms')>Family SMS / SMS famille</option>
                            <option value="summary" @selected(old('feature') === 'summary')>Case summary / Résumé dossier</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3" id="tone-wrap">
                            <label class="form-label fw-semibold">Tone / Ton</label>
                            <select name="tone" class="form-select">
                                <option value="formal">Formal / Formel</option>
                                <option value="warm">Warm / Chaleureux</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="sms-wrap" style="display:none;">
                            <label class="form-label fw-semibold">SMS type</label>
                            <select name="sms_purpose" class="form-select">
                                <option value="pickup">Pickup / Levée</option>
                                <option value="payment">Payment / Paiement</option>
                                <option value="schedule">Schedule / Planning</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3" id="photo-wrap">
                        <label class="form-label fw-semibold">Photo</label>
                        <input type="file" name="photo" id="photo" class="form-control" accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">Optional JPG/PNG/WebP. Shown on the result and used as visual context for faire-part/condolences.</div>
                        <img id="photo-preview" class="photo-preview mt-3" alt="Preview">
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" value="1" id="save_notice" name="save_notice" checked>
                        <label class="form-check-label" for="save_notice">
                            Save faire-part / condolences
                        </label>
                    </div>

                    <button type="submit" class="btn btn-accent btn-lg">Generate / Générer</button>
                </form>
            @endif
        </div>
    </div>

    <div class="col-lg-5">
        <div class="surface-card p-4 h-100">
            <h5 class="fw-bold mb-3">Recent saved texts</h5>
            @forelse($notices as $notice)
                <div class="border-bottom py-3">
                    <div class="fw-semibold">{{ $notice->deceased->full_name ?? 'N/A' }}</div>
                    <div class="small text-muted mb-1">
                        {{ $notice->theme }} · {{ $notice->language }} · {{ $notice->created_at?->diffForHumans() }}
                    </div>
                    <div class="small" style="white-space: pre-line;">
                        {{ \Illuminate\Support\Str::limit($notice->announcement, 160) }}
                    </div>
                </div>
            @empty
                <p class="text-muted mb-0">No saved texts yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const feature = document.getElementById('feature');
    const smsWrap = document.getElementById('sms-wrap');
    const toneWrap = document.getElementById('tone-wrap');
    const photoWrap = document.getElementById('photo-wrap');
    const photo = document.getElementById('photo');
    const preview = document.getElementById('photo-preview');

    function refresh() {
        const value = feature.value;
        smsWrap.style.display = value === 'sms' ? 'block' : 'none';
        toneWrap.style.display = value === 'condolences' ? 'block' : 'none';
        photoWrap.style.display = (value === 'faire_part' || value === 'condolences') ? 'block' : 'none';
    }

    feature.addEventListener('change', refresh);
    refresh();

    photo?.addEventListener('change', function () {
        const file = this.files?.[0];
        if (!file) {
            preview.style.display = 'none';
            preview.removeAttribute('src');
            return;
        }
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    });
});
</script>
@endpush
