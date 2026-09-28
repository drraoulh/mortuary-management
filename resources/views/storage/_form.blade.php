@if($errors->any())
    <div class="alert alert-danger rounded-4">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-3">
    <div class="col-md-4">
        <label for="room_number" class="form-label fw-semibold">Room number</label>
        <input type="text" name="room_number" id="room_number" class="form-control"
               value="{{ old('room_number', $storage->room_number ?? '') }}" required>
        <div class="form-text">Use the same name staff type as "Room" when registering a body.</div>
    </div>
    <div class="col-md-4">
        <label for="capacity" class="form-label fw-semibold">Capacity</label>
        <input type="number" name="capacity" id="capacity" min="1" class="form-control"
               value="{{ old('capacity', $storage->capacity ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label for="status" class="form-label fw-semibold">Status</label>
        <select name="status" id="status" class="form-select" required>
            @foreach($statuses as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $storage->status ?? 'available') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>
