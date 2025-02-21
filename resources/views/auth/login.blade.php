<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>

    <div class="auth-container">
        <h2>Iniciar Sesión</h2>

        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Iniciar Sesión</button>
        </form>

        <p>¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a></p>
    </div>

</body>
</html>
