<!DOCTYPE html>
<html>
<head>
    <title>Storage Rooms</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

    <h2>Storage Rooms</h2>

    <a href="{{ route('storage.create') }}" class="btn btn-primary mb-3">
        Add Room
    </a>

    <div class="card p-3">

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Room Number</th>
                    <th>Capacity</th>
                    <th>Status</th>
                    
                </tr>
            </thead>

            <tbody>
                @foreach($rooms as $room)
                <tr>
                    <td>{{ $room->room_number }}</td>
                    <td>{{ $room->capacity }}</td>
                    <td>{{ $room->status }}</td>
                  <td>
    @foreach($room->deceaseds as $deceased)
        {{ $deceased->full_name }}
    @endforeach
</td>
<th>Occupant</th>  
                </tr>
                @endforeach
            </tbody>

        </table>

    </div>

</div>

</body>
</html>