<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Page</title>
</head>
<body>
    <h1>Halo selamat datang, {{ $user->name }}</h1>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        @method('DELETE')

        <button type="submit">Logout</button>
    </form>
</body>
</html>