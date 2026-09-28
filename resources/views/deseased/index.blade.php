@extends('layouts.app')

@section('content')

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Deceased Records</h2>
            <p class="text-muted mb-0">
                Manage registered deceased persons
            </p>
        </div>

        <a href="{{ route('deceased.create') }}"
           class="btn btn-primary">
            ➕ Add New Body
        </a>
    </div>


    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif


    @if(isset($deceaseds) && count($deceaseds) > 0)

        <div class="row">

            @foreach($deceaseds as $d)

                <div class="col-md-6 col-lg-4 mb-4">

                    <div class="card shadow-sm h-100">

                        <div class="card-body">

                            <h5 class="card-title fw-bold">
                                {{ $d->full_name }}
                            </h5>

                            @if(!empty($d->gender))
                                <p class="mb-1">
                                    <strong>Gender:</strong>
                                    {{ $d->gender }}
                                </p>
                            @endif

                            @if(!empty($d->date_of_birth))
                                <p class="mb-1">
                                    <strong>Date of birth:</strong>
                                    {{ $d->date_of_birth }}
                                </p>
                            @endif

                            @if(!empty($d->date_of_death))
                                <p class="mb-3">
                                    <strong>Date of death:</strong>
                                    {{ $d->date_of_death }}
                                </p>
                            @endif


                            <div class="d-flex gap-2 flex-wrap">

                                {{-- AI Faire-part --}}
                                <a
                                    href="{{ route('faire-part.generate', $d->id) }}"
                                    class="btn btn-success"
                                >
                                    🕊️ AI Faire-part
                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    @else

        <div class="alert alert-info">
            No deceased records found.
        </div>

    @endif

</div>

@endsection