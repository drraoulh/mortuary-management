@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Générer un faire-part</h2>
                        <p class="text-muted">
                            Annonce funéraire respectueuse via Hugging Face (ou fallback local).
                        </p>
                    </div>

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

                    <form method="POST" action="{{ route('faire-part.generate') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-bold">Défunt</label>
                            <select name="deceased_id" class="form-select" required>
                                <option value="">-- Sélectionner --</option>
                                @foreach($deceaseds as $deceased)
                                    <option value="{{ $deceased->id }}" @selected(old('deceased_id') == $deceased->id)>
                                        {{ $deceased->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Langue</label>
                            <select name="language" class="form-select">
                                <option value="fr" @selected(old('language', 'fr') === 'fr')>Français</option>
                                <option value="en" @selected(old('language') === 'en')>English</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Photographie (optionnelle)</label>
                            <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/webp">
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" value="1" name="save_notice" id="save_notice" checked>
                            <label class="form-check-label" for="save_notice">Enregistrer dans les annonces</label>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('ai.index') }}" class="btn btn-secondary">Assistant IA</a>
                            <button type="submit" class="btn btn-success">Générer le faire-part</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
