<h1>Storage Room Details</h1>

<p>Room: {{ $storage->room_number }}</p>
<p>Capacity: {{ $storage->capacity }}</p>
<p>Status: {{ $storage->status }}</p>

<a href="{{ route('storage.index') }}">Back</a>