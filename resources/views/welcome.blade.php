<!DOCTYPE html>
<html>
<head>
    <title>Mortuary Management System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body{
            background:url('https://images.unsplash.com/photo-1517841905240-472988babdf9');
            background-size:cover;
            background-position:center;
            height:100vh;
        }

        .overlay{
            background:rgba(0,0,0,0.7);
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
        }

        .card-home{
            background:white;
            border-radius:20px;
            padding:50px;
            max-width:700px;
            text-align:center;
            box-shadow:0px 0px 20px rgba(0,0,0,0.3);
        }

        h1{
            color:#0d6efd;
            font-weight:bold;
        }

        p{
            font-size:18px;
        }

    </style>

</head>

<body>

<div class="overlay">

    <div class="card-home">

        <h1>⚰ Mortuary Management System</h1>

        <hr>

        <p>
            Welcome to the Mortuary Management System.
            This platform helps staff manage deceased records,
            storage rooms, payments, pickup scheduling,
            notifications and verification services efficiently.
        </p>

        <p>
            Every registered deceased receives a unique
            identification code for tracking and verification.
        </p>

        <div class="mt-4">

            <a href="{{ route('login') }}"
               class="btn btn-primary btn-lg">
                Login
            </a>

            <a href="{{ route('register') }}"
               class="btn btn-success btn-lg">
                Register
            </a>

        </div>

    </div>

</div>

</body>
</html>