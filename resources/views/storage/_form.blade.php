@if($errors->any())
    <div class="alert alert-danger border-0">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mb-3">
    <label class="form-label fw-semibold" for="room_number">Room number</label>
    <input type="text" id="room_number" name="room_number" class="form-control"
           value="{{ old('room_number', $storage->room_number ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold" for="capacity">Capacity</label>
    <input type="number" id="capacity" name="capacity" class="form-control" min="1"
           value="{{ old('capacity', $storage->capacity ?? '') }}" required>
</div>

<div class="mb-4">
    <label class="form-label fw-semibold" for="status">Status</label>
    <select id="status" name="status" class="form-select" required>
        @foreach($statuses as $value => $label)
            <option value="{{ $value }}" @selected(old('status', $storage->status ?? 'available') === $value)>{{ $label }}</option>
        @endforeach
    </select>
</div>
