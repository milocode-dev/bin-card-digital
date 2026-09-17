<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login Page</title>
</head>
<body>
    <form action="{{ route('login') }}" method="POST">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" placeholder="Isi email">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Isi password">

        <button type="submit">Login</button>
    </form>
</body>
</html>