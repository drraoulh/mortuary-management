<!DOCTYPE html>
<html>
<head>
    <title>Register Deceased Information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow p-4">

        <h2 class="mb-4">Register New Body</h2>

        <form action="{{ route('deceased.store') }}" method="POST">
            @csrf
          

            <div class="mb-3">
    <label for="location_address" class="form-label">
        Location Address
    </label>

    <input
        type="text"
        name="location_address"
        id="location_address"
        class="form-control"
        value="{{ old('location_address') }}"
        placeholder="Enter location address"
    >
</div>

<div class="mb-3">
    <label for="latitude" class="form-label">
        Latitude
    </label>

    <input
        type="text"
        name="latitude"
        id="latitude"
        class="form-control"
        value="{{ old('latitude') }}"
        placeholder="Example: 3.8480"
    >
</div>

<div class="mb-3">
    <label for="longitude" class="form-label">
        Longitude
    </label>

    <input
        type="text"
        name="longitude"
        id="longitude"
        class="form-control"
        value="{{ old('longitude') }}"
        placeholder="Example: 11.5021"
    >
</div>
            <div class="mb-3">
                <label>Full Name</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Gender</label>
                <select name="gender" class="form-control" required>
                    <option value="">Select Gender</option>
                    <option>Male</option>
                    <option>Female</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Date of Birth</label>
                <input type="date" name="date_of_birth" class="form-control">
            </div>

            <div class="mb-3">
                <label>Date of Death</label>
                <input type="date" name="date_of_death" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Admission Date</label>
                <input type="date" name="admission_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Release Date</label>
                <input type="date" name="release_date" class="form-control">
            </div>

            <div class="mb-3">
                <label>Room Name</label>
                <input type="text" name="room_name" class="form-control" placeholder="Cobard 2">
            </div>

            <div class="mb-3">
                <label>Room Type</label>
                <select name="room_type" class="form-control">
                    <option value="normal">Normal</option>
                    <option value="vip">VIP</option>
                    <option value="vvip">VVIP</option>
                </select>
                 <label>Cause of Death</label>
    <input type="text" name="cause_of_death" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success">
                Save Record
            </button>

            <a href="{{ route('deceased.index') }}" class="btn btn-secondary">
                Back
            </a>


        </form>

    </div>
</div>

</body>
</html>