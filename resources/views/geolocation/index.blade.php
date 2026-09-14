@extends('layouts.app')

@section('content')

<div class="container py-4">

    {{-- Page Header --}}
    <div class="mb-4">
        <h2 class="fw-bold">📍 Find Nearby Mortuaries</h2>
        <p class="text-muted">
            Enter a city and quarter to find available mortuary facilities nearby.
        </p>
    </div>

    {{-- Search Form --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">

            <form method="POST" action="{{ route('geolocation.search') }}">
                @csrf

                <div class="mb-3">
                    <label for="location" class="form-label fw-semibold">
                        Your location
                    </label>

                    <input
                        type="text"
                        name="location"
                        id="location"
                        class="form-control form-control-lg"
                        placeholder="Example: Yaoundé, Odza"
                        value="{{ old('location', $location ?? '') }}"
                        required
                    >

                    <small class="text-muted">
                        Example: Yaoundé, Odza or Douala, Akwa
                    </small>
                </div>

                <button type="submit" class="btn btn-primary px-4">
                    🔍 Search Mortuaries
                </button>
            </form>

        </div>
    </div>


    {{-- Error Message --}}
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif


    {{-- Location Found --}}
    @isset($displayName)

        <div class="alert alert-info mb-4">
            <strong>📍 Location:</strong>
            {{ $displayName }}
        </div>

    @endisset


    {{-- Mortuary Results --}}
    @isset($nearbyMortuaries)

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h3 class="fw-bold mb-1">
                    🏥 Nearby Mortuaries
                </h3>

                <p class="text-muted mb-0">
                    {{ count($nearbyMortuaries) }}
                    mortuary facilities found
                </p>
            </div>

        </div>


        @if(count($nearbyMortuaries) > 0)

            <div class="row">

                @foreach($nearbyMortuaries as $mortuary)

                    <div class="col-md-6 col-lg-4 mb-4">

                        <div class="card h-100 shadow-sm border-0">

                            <div class="card-body p-4">

                                {{-- Mortuary Name --}}
                                <h5 class="card-title fw-bold mb-3">
                                    🏥 {{ $mortuary['name'] }}
                                </h5>


                                {{-- Quarter --}}
                                <p class="mb-2">
                                    <strong>📍 Area:</strong>
                                    {{ $mortuary['quarter'] }},
                                    {{ $mortuary['city'] }}
                                </p>


                                {{-- Address --}}
                                @if($mortuary['address'])
                                    <p class="text-muted mb-2">
                                        {{ $mortuary['address'] }}
                                    </p>
                                @endif


                                {{-- Distance --}}
                                <p class="mb-3">
                                    <span class="badge bg-primary">
                                        📏 {{ $mortuary['distance'] }} km away
                                    </span>
                                </p>


                                {{-- Description --}}
                                @if($mortuary['description'])
                                    <p class="card-text text-muted">
                                        {{ $mortuary['description'] }}
                                    </p>
                                @endif


                                {{-- Phone --}}
                                @if($mortuary['phone'])
                                    <p class="mb-0">
                                        📞 {{ $mortuary['phone'] }}
                                    </p>
                                @endif

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <div class="alert alert-warning">
                No mortuaries were found for this city.
            </div>

        @endif

    @endisset

</div>
{{-- Simulated Map --}}
@isset($nearbyMortuaries)

    @if(count($nearbyMortuaries) > 0)

        <div class="card shadow-sm border-0 mt-3 mb-4">

            <div class="card-body p-4">

                <h3 class="fw-bold mb-1">
                    🗺️ Mortuary Location Map
                </h3>

                <p class="text-muted mb-3">
                    Map showing the available mortuary facilities.
                </p>

                <div
                    id="mortuary-map"
                    style="
                        height: 450px;
                        width: 100%;
                        border-radius: 12px;
                        overflow: hidden;
                    ">
                </div>

            </div>

        </div>

    @endif

@endisset


{{-- Leaflet Map --}}
@isset($nearbyMortuaries)

    @if(count($nearbyMortuaries) > 0)

        <link
            rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        >

        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {

                const mortuaries = @json($nearbyMortuaries);

                if (!mortuaries.length) {
                    return;
                }

                const map = L.map('mortuary-map');

                L.tileLayer(
                    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    {
                        attribution: '&copy; OpenStreetMap contributors'
                    }
                ).addTo(map);

                const markers = [];

                mortuaries.forEach(function (mortuary) {

                    const marker = L.marker([
                        mortuary.latitude,
                        mortuary.longitude
                    ]).addTo(map);

                    marker.bindPopup(`
                        <strong>${mortuary.name}</strong><br>
                        ${mortuary.quarter}, ${mortuary.city}<br>
                        ${mortuary.distance} km away
                    `);

                    markers.push(marker);
                });

                const group = L.featureGroup(markers);

                map.fitBounds(group.getBounds(), {
                    padding: [30, 30]
                });

            });
        </script>

    @endif

@endisset
@endsection