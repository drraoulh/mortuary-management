@extends('layouts.app')

@section('title', 'Add Storage Room')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="page-hero">
            <span class="eyebrow">Storage</span>
            <h1>Add a storage room</h1>
        </div>

        <form action="{{ route('storage.store') }}" method="POST" class="surface-card p-4">
            @csrf
            @include('storage._form')

            <div class="d-flex gap-2">
                <button class="btn btn-accent">Save room</button>
                <a href="{{ route('storage.index') }}" class="btn btn-soft">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
