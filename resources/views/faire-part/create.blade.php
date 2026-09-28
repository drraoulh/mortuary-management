@extends('layouts.app')

@section('content')

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card shadow-sm border-0">

                <div class="card-body p-5">

                    <div class="text-center mb-4">

                        <h2 class="fw-bold">
                            🕊️ Generate Funeral Faire-part
                        </h2>

                        <p class="text-muted">
                            Select the deceased person and optionally upload a photograph.
                        </p>

                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
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

                    <form
                        method="POST"
                        action="{{ route('faire-part.generate') }}"
                        enctype="multipart/form-data"
                    >

                        @csrf

                        <div class="mb-4">

                            <label class="form-label fw-bold">
                                Select deceased person
                            </label>

                            <select
                                name="deceased_id"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    -- Select deceased person --
                                </option>

                                @foreach($deceaseds as $deceased)

                                    <option
                                        value="{{ $deceased->id }}"
                                        {{ old('deceased_id') == $deceased->id ? 'selected' : '' }}
                                    >
                                        {{ $deceased->full_name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="mb-4">

                            <label class="form-label fw-bold">
                                Upload photograph
                            </label>

                            <input
                                type="file"
                                name="photo"
                                class="form-control"
                                accept="image/jpeg,image/png,image/webp"
                            >

                            <div class="form-text">
                                Optional. You can upload a JPG, PNG or WebP photograph.
                            </div>

                        </div>

                        <div class="d-flex justify-content-between">

                            <a
                                href="{{ route('deceased.index') }}"
                                class="btn btn-secondary"
                            >
                                ← Back
                            </a>

                            <button
                                type="submit"
                                class="btn btn-success"
                            >
                                🕊️ Generate Faire-part
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection