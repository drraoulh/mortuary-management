<!DOCTYPE html>
<html>
<head>
    <title>Deceased Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2>Deceased Records</h2>
    @if(session('success'))

<div class="alert alert-success">
    {{ session('success') }}
</div>

@endif
   <a
    href="{{ route('faire-part.create') }}"
    class="btn btn-success mb-3"
>
    Générer Faire-part
</a>
   <a
    href="{{ route('ai.index') }}"
    class="btn btn-outline-success mb-3"
>
    Assistant IA
</a>
    <a href="{{ route('deceased.create') }}" class="btn btn-primary mb-3">
        Register New Body
    </a>
    <div style="margin-top: 10px; margin-bottom: 20px;">
    
</div>

    <form method="GET" class="mb-3">
        <input type="text" name="search" class="form-control" placeholder="Search by name">
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Gender</th>
                <th>Date of Birth</th>
                <th>Date of Death</th>
                <th>Room</th>
                <th>Room Type</th>
                <th>Price</th>
                
            </tr>
        </thead>

        <tbody>
            @foreach($deceaseds as $deceased)
            <tr>
                <td>{{ $deceased->full_name }}</td>
                <td>{{ $deceased->gender }}</td>
                <td>{{ $deceased->date_of_birth }}</td>
                <td>{{ $deceased->date_of_death }}</td>
                <td>{{ $deceased->room_name }}</td>
                <td>{{ $deceased->room_type }}</td>
                <td>{{ $deceased->price }}</td>
               
            </tr>
        
            @endforeach
        </tbody>
    </table>
</div>
<script>
    setTimeout(function () {
        let msg = document.getElementById('success-message');
        if (msg) {
            msg.style.display = 'none';
        }
    }, 3000);
</script>

</body>
</html>