@extends('layouts.app')

@section('title', 'Find mortuaries')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #geo-map {
        height: min(62vh, 560px);
        width: 100%;
        border-radius: 16px;
        border: 1px solid #d7e0ea;
        z-index: 1;
    }
    .mortuary-item {
        border: 1px solid #d7e0ea;
        border-radius: 14px;
        padding: 1rem;
        background: #fff;
        cursor: pointer;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .mortuary-item:hover,
    .mortuary-item.active {
        border-color: #0f6a5a;
        box-shadow: 0 8px 20px rgba(15, 106, 90, 0.08);
    }
    .user-dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #1d4f91;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #1d4f91;
    }
</style>
@endpush

@section('content')
<div class="page-hero d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
        <h1>Find nearby mortuaries</h1>
        <p>Use your real GPS position or search a Cameroon address. Results use OpenStreetMap.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="surface-card p-4 mb-3">
            <form method="POST" action="{{ route('geolocation.search') }}" id="geo-form">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold" for="location">Address / quarter</label>
                    <input
                        type="text"
                        name="location"
                        id="location"
                        class="form-control form-control-lg"
                        placeholder="Example: Odza, Yaoundé"
                        value="{{ old('location', $location ?? '') }}"
                    >
                    <div class="form-text">Real geocoding via OpenStreetMap Nominatim.</div>
                </div>

                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $userLocation['latitude'] ?? '') }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $userLocation['longitude'] ?? '') }}">

                <div class="mb-3">
                    <label class="form-label fw-semibold" for="radius_km">Search radius</label>
                    <select name="radius_km" id="radius_km" class="form-select">
                        @foreach([10, 20, 40, 80, 120] as $radius)
                            <option value="{{ $radius }}" @selected((int) old('radius_km', $radiusKm ?? 40) === $radius)>
                                {{ $radius }} km
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-accent" id="use-gps-btn">
                        Use my real location (GPS)
                    </button>
                    <button type="submit" class="btn btn-soft" id="search-address-btn">
                        Search by address
                    </button>
                </div>

                <div id="gps-status" class="small text-muted mt-3"></div>
            </form>
        </div>

        @if(!empty($displayName))
            <div class="alert alert-success border-0">
                <strong>{{ ($searchMode ?? '') === 'gps' ? 'GPS location' : 'Resolved address' }}:</strong><br>
                {{ $displayName }}
            </div>
        @endif

        <div class="surface-card p-3">
            <div class="d-flex justify-content-between align-items-center mb-3 px-1">
                <h5 class="mb-0 fw-bold">
                    @if(isset($nearbyMortuaries) && $nearbyMortuaries->count())
                        Nearby results
                    @else
                        All mortuaries
                    @endif
                </h5>
                <span class="badge text-bg-light border">
                    {{ isset($nearbyMortuaries) && $nearbyMortuaries->count() ? $nearbyMortuaries->count() : $allMortuaries->count() }}
                </span>
            </div>

            <div class="d-flex flex-column gap-2" style="max-height: 420px; overflow:auto;">
                @php
                    $list = (isset($nearbyMortuaries) && $nearbyMortuaries->count())
                        ? $nearbyMortuaries
                        : $allMortuaries;
                @endphp

                @forelse($list as $mortuary)
                    <div
                        class="mortuary-item"
                        data-id="{{ $mortuary['id'] }}"
                        data-lat="{{ $mortuary['latitude'] }}"
                        data-lng="{{ $mortuary['longitude'] }}"
                    >
                        <div class="fw-semibold">{{ $mortuary['name'] }}</div>
                        <div class="small text-muted mb-2">
                            {{ $mortuary['quarter'] }}, {{ $mortuary['city'] }}
                            @if(!empty($mortuary['distance']))
                                · {{ $mortuary['distance'] }} km
                            @endif
                        </div>
                        @if(!empty($mortuary['address']))
                            <div class="small mb-2">{{ $mortuary['address'] }}</div>
                        @endif
                        <div class="d-flex gap-2 flex-wrap">
                            @if(!empty($mortuary['phone']))
                                <a class="btn btn-sm btn-outline-secondary" href="tel:{{ $mortuary['phone'] }}">Call</a>
                            @endif
                            <a class="btn btn-sm btn-soft" target="_blank" rel="noopener" href="{{ $mortuary['google_maps_url'] }}">
                                Directions
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-muted p-2">No mortuaries found in this radius. Increase the radius or try another area.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="surface-card p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-0">Live map</h5>
                    <div class="small text-muted">OpenStreetMap tiles · real coordinates</div>
                </div>
                <div class="small text-muted d-flex align-items-center gap-3">
                    <span class="d-inline-flex align-items-center gap-2"><span class="user-dot"></span> You</span>
                    <span>Mortuary markers</span>
                </div>
            </div>
            <div id="geo-map"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const allMortuaries = @json((isset($nearbyMortuaries) && count($nearbyMortuaries)) ? [] : ($allMortuaries ?? []));
    const nearbyMortuaries = @json($nearbyMortuaries ?? []);
    const userLocation = @json($userLocation ?? null);
    const mapMortuaries = nearbyMortuaries.length ? nearbyMortuaries : allMortuaries;

    const map = L.map('geo-map', { scrollWheelZoom: true });
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const markers = [];
    const markerById = {};

    function addMortuaryMarker(mortuary) {
        const marker = L.marker([mortuary.latitude, mortuary.longitude]).addTo(map);
        marker.bindPopup(`
            <strong>${mortuary.name}</strong><br>
            ${mortuary.quarter}, ${mortuary.city}<br>
            ${mortuary.distance != null ? mortuary.distance + ' km away<br>' : ''}
            <a href="${mortuary.google_maps_url}" target="_blank" rel="noopener">Open directions</a>
        `);
        markers.push(marker);
        markerById[mortuary.id] = marker;
        return marker;
    }

    mapMortuaries.forEach(addMortuaryMarker);

    let userMarker = null;
    if (userLocation && userLocation.latitude && userLocation.longitude) {
        userMarker = L.circleMarker([userLocation.latitude, userLocation.longitude], {
            radius: 9,
            color: '#1d4f91',
            fillColor: '#1d4f91',
            fillOpacity: 0.9,
            weight: 2
        }).addTo(map);
        userMarker.bindPopup(`<strong>Your location</strong><br>${userLocation.label || 'GPS'}`);
        markers.push(userMarker);
    }

    if (markers.length) {
        const group = L.featureGroup(markers);
        map.fitBounds(group.getBounds().pad(0.18));
    } else {
        map.setView([3.8480, 11.5021], 7);
    }

    document.querySelectorAll('.mortuary-item').forEach((item) => {
        item.addEventListener('click', () => {
            document.querySelectorAll('.mortuary-item').forEach((el) => el.classList.remove('active'));
            item.classList.add('active');
            const id = item.dataset.id;
            const marker = markerById[id];
            if (marker) {
                map.setView(marker.getLatLng(), 14, { animate: true });
                marker.openPopup();
            }
        });
    });

    const form = document.getElementById('geo-form');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const locationInput = document.getElementById('location');
    const status = document.getElementById('gps-status');

    document.getElementById('use-gps-btn')?.addEventListener('click', function () {
        if (!navigator.geolocation) {
            status.textContent = 'Geolocation is not supported by this browser.';
            return;
        }

        status.textContent = 'Getting your GPS position…';
        this.disabled = true;

        navigator.geolocation.getCurrentPosition(
            (position) => {
                latInput.value = position.coords.latitude.toFixed(7);
                lngInput.value = position.coords.longitude.toFixed(7);
                locationInput.value = '';
                status.textContent = `GPS locked: ${latInput.value}, ${lngInput.value}`;
                form.submit();
            },
            (error) => {
                this.disabled = false;
                status.textContent = 'Unable to get GPS: ' + (error.message || 'permission denied');
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
        );
    });

    document.getElementById('search-address-btn')?.addEventListener('click', function (event) {
        // Clear GPS fields so address search is used.
        if (locationInput.value.trim() !== '') {
            latInput.value = '';
            lngInput.value = '';
        }
    });
});
</script>
@endpush
