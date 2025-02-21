<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>

    <div class="auth-container">
        <h2>Registro</h2>

        @if ($errors->any())
            <div class="alert error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <label for="nombre">Nombre Completo</label>
            <input type="text" name="nombre" id="nombre" required>

            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" id="email" required>

            <label for="telefono">Teléfono</label>
            <div class="phone-input">
                <select name="prefijo" id="prefijo">
                    <option value="+34">🇪🇸 +34 (España)</option>
                    <option value="+33">🇫🇷 +33 (Francia)</option>
                    <option value="+1">🇺🇸 +1 (EE.UU.)</option>
                    <option value="+351">🇵🇹 +351 (Portugal)</option>
                </select>
                <input type="text" name="telefono" id="telefono" placeholder="123456789">
            </div>

            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" required>

            <label for="password_confirmation">Confirmar Contraseña</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required>

            <button type="submit">Registrarse</button>
        </form>

        <p>¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión aquí</a></p>
    </div>

</body>
</html>
