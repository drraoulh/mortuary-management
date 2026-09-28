@extends('layouts.app')

@section('title', 'Add Storage Room')

@section('content')

<div class="container pb-4" style="max-width: 760px;">

    <div class="page-header">
        <span class="eyebrow"><i class="bi bi-door-closed me-1"></i> Storage</span>
        <h1>Add storage room</h1>
    </div>

    <div class="panel">
        <div class="panel-body p-4">
            <form method="POST" action="{{ route('storage.store') }}">
                @csrf

                @include('storage._form', ['storage' => null])

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Save room</button>
                    <a href="{{ route('storage.index') }}" class="btn btn-pink-soft rounded-pill px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
