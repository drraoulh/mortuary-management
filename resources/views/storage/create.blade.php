<h1>Add Storage Room</h1>

<form method="POST" action="{{ route('storage.store') }}">
    @csrf

    <input type="text" name="room_number" placeholder="Room Number"><br><br>

    <input type="number" name="capacity" placeholder="Capacity"><br><br>

    <input type="text" name="status" placeholder="Status (available/occupied)"><br><br>

    <button type="submit">Save</button>
</form>