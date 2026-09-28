<h1>Edit Storage Room</h1>

<form method="POST" action="{{ route('storage.update', $storage->id) }}">
    @csrf
    @method('PUT')

    <input type="text" name="room_number" value="{{ $storage->room_number }}"><br><br>

    <input type="number" name="capacity" value="{{ $storage->capacity }}"><br><br>

    <input type="text" name="status" value="{{ $storage->status }}"><br><br>

    <button type="submit">Update</button>
</form>