<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff/Faculty Login</title>
    <style>
        body {
            font-family: Figtree, sans-serif;
            background: #f3f4f6;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: #fff;
            padding: 2rem 2.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 8px 32px 0 rgba(31, 41, 55, 0.15);
            text-align: center;
        }

        .login-link {
            display: inline-block;
            font-weight: 600;
            color: #2563eb;
            border: 2px solid #2563eb;
            padding: 0.75rem 2rem;
            border-radius: 0.375rem;
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
        }

        .login-link:hover {
            background: #2563eb;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <a href="{{ route('auth.StaffLogin') }}" class="login-link">
            Staff/Faculty Login
        </a>
    </div>
</body>

</html>