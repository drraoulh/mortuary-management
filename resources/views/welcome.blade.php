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
            background:linear-gradient(135deg, rgba(131,24,67,0.85), rgba(236,72,153,0.7));
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
            color:#db2777;
            font-weight:bold;
        }

        .btn-pink{
            background:#db2777;
            border-color:#db2777;
            color:#fff;
        }

        .btn-pink:hover{
            background:#be185d;
            border-color:#be185d;
            color:#fff;
        }

        .btn-outline-pink{
            border-color:#db2777;
            color:#db2777;
        }

        .btn-outline-pink:hover{
            background:#fce7f3;
            color:#be185d;
        }

        p{
            font-size:18px;
        }

    </style>

</head>

<body>

<div class="overlay">

    <div class="card-home">

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

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

            @auth
                <a href="{{ route('dashboard') }}"
                   class="btn btn-pink btn-lg">
                    Go to my dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="btn btn-pink btn-lg">
                    Login
                </a>

                <a href="{{ route('register') }}"
                   class="btn btn-outline-pink btn-lg">
                    Register
                </a>
            @endauth

        </div>

    </div>

</div>

</body>
</html>