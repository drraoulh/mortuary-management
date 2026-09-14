@extends('layouts.app')

@section('content')

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold">
                🕊️ Funeral Faire-part
            </h2>

            <p class="text-muted mb-0">
                AI-generated funeral announcement
            </p>
        </div>

        <button
            onclick="window.print()"
            class="btn btn-primary">
            🖨️ Print
        </button>

    </div>


    <div class="card shadow-sm border-0">

        <div class="card-body p-5">

            <div
                style="
                    max-width: 800px;
                    margin: auto;
                    font-family: Georgia, serif;
                    line-height: 1.8;
                    white-space: pre-line;
                "
            >

                {!! nl2br(e($fairePart)) !!}

            </div>

        </div>

    </div>


    <div class="mt-4">

        <a
            href="{{ url()->previous() }}"
            class="btn btn-secondary"
        >
            ← Back
        </a>

    </div>

</div>

@endsection