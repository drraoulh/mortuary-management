@extends('layouts.app')

@section('title', 'Edit Storage Room')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="page-hero">
            <span class="eyebrow">Storage</span>
            <h1>Edit room {{ $storage->room_number }}</h1>
        </div>

        <form action="{{ route('storage.update', $storage) }}" method="POST" class="surface-card p-4">
            @csrf
            @method('PUT')
            @include('storage._form')

            <div class="d-flex gap-2">
                <button class="btn btn-accent">Save changes</button>
                <a href="{{ route('storage.index') }}" class="btn btn-soft">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
