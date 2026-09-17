@extends('layouts.app')

@section('title', 'Faire-part')

@section('content')
<div class="page-hero">
    <h1>Funeral Faire-part</h1>
    <p>Select language (FR/EN), optionally add a photograph, then generate.</p>
</div>

<div class="surface-card p-4 p-md-5" style="max-width: 760px; margin: 0 auto;">
    @if($errors->any())
        <div class="alert alert-danger border-0">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('faire-part.generate') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-semibold">Deceased</label>
            <select name="deceased_id" class="form-select" required>
                <option value="">-- Select --</option>
                @foreach($deceaseds as $deceased)
                    <option value="{{ $deceased->id }}" @selected(old('deceased_id') == $deceased->id)>
                        {{ $deceased->full_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold d-block">Language</label>
            <div class="btn-group lang-toggle" role="group">
                <input type="radio" class="btn-check" name="language" id="fp-lang-fr" value="fr" @checked(old('language', 'fr') === 'fr')>
                <label class="btn btn-outline-success" for="fp-lang-fr">FR</label>
                <input type="radio" class="btn-check" name="language" id="fp-lang-en" value="en" @checked(old('language') === 'en')>
                <label class="btn btn-outline-success" for="fp-lang-en">EN</label>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Photograph</label>
            <input type="file" name="photo" id="fp-photo" class="form-control" accept="image/jpeg,image/png,image/webp">
            <img id="fp-preview" class="photo-preview mt-3" alt="Preview">
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" value="1" name="save_notice" id="fp-save" checked>
            <label class="form-check-label" for="fp-save">Save announcement</label>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('ai.index') }}" class="btn btn-soft">AI Assistant</a>
            <button type="submit" class="btn btn-accent">Generate faire-part</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('fp-photo')?.addEventListener('change', function () {
    const preview = document.getElementById('fp-preview');
    const file = this.files?.[0];
    if (!file) {
        preview.style.display = 'none';
        return;
    }
    preview.src = URL.createObjectURL(file);
    preview.style.display = 'block';
});
</script>
@endpush
