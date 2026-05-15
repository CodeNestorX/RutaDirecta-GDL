<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="RutaDirecta GDL — Inicia sesión para guardar tus rutas favoritas." />
    <title>RutaDirecta GDL — Iniciar sesión</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="/css/rutas.css" />
    <link rel="stylesheet" href="/css/auth.css" />
</head>
<body>

<!-- ── Top Navigation ──────────────────────────────────── -->
<nav class="top-nav" role="navigation" aria-label="Navegación">
    <a href="{{ route('rutas.index') }}" class="back-btn" aria-label="Regresar al listado de rutas">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
    </a>
    <h1>Iniciar sesión</h1>
    <span class="linea-badge" aria-hidden="true">ACCESO</span>
</nav>

<!-- ── Contenido principal ─────────────────────────────── -->
<main class="page" id="main-content">

    <!-- ── Hero de bienvenida ──────────────────────────── -->
    <div class="auth-hero" aria-hidden="true">
        <div class="auth-hero-icon">
            <!-- Ícono de bus -->
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M17 20H7v1a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-1H3V8c0-2.21 3.58-4 8-4s8 1.79 8 4v12h-1v1a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1v-1zm2-9H5v4h14V11zm0 5H5v2h14v-2zM7.5 14a1 1 0 1 1 0-2 1 1 0 0 1 0 2zm9 0a1 1 0 1 1 0-2 1 1 0 0 1 0 2zM5 8v2h14V8H5z"/>
            </svg>
        </div>
        <div>
            <p class="auth-hero-title">RutaDirecta GDL</p>
            <p class="auth-hero-subtitle">Tu transporte público en tiempo real</p>
        </div>
    </div>

    <!-- ── Tarjeta del formulario ──────────────────────── -->
    <section class="auth-card" aria-label="Formulario de inicio de sesión">

        <h2 class="auth-card-title">Bienvenido de vuelta</h2>
        <p class="auth-card-subtitle">Inicia sesión para guardar tus rutas favoritas</p>

        <!-- Errores globales -->
        @if ($errors->any())
        <div class="auth-alert auth-alert--error" role="alert" aria-live="assertive">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span>{{ $errors->first() }}</span>
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <!-- Correo electrónico -->
            <div class="auth-field">
                <label class="auth-label" for="login-email">Correo electrónico</label>
                <input class="auth-input @error('email') auth-input--error @enderror"
                       type="email"
                       id="login-email"
                       name="email"
                       value="{{ old('email') }}"
                       placeholder="tucorreo@ejemplo.com"
                       autocomplete="email"
                       required
                       aria-describedby="@error('email') login-email-error @enderror" />
                @error('email')
                    <p class="auth-field-error" id="login-email-error" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contraseña -->
            <div class="auth-field">
                <label class="auth-label" for="login-password">Contraseña</label>
                <div class="auth-input-wrapper">
                    <input class="auth-input @error('password') auth-input--error @enderror"
                           type="password"
                           id="login-password"
                           name="password"
                           placeholder="••••••••"
                           autocomplete="current-password"
                           required />
                    <button type="button"
                            class="auth-toggle-pw"
                            aria-label="Mostrar u ocultar contraseña"
                            data-target="login-password">
                        <!-- Ojo abierto -->
                        <svg class="icon-eye" width="18" height="18" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <!-- Ojo cerrado (oculto por defecto) -->
                        <svg class="icon-eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             style="display:none">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="auth-field-error" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember me -->
            <div class="auth-remember">
                <label class="auth-checkbox-label">
                    <input type="checkbox" name="remember" id="login-remember" />
                    <span class="auth-checkbox-custom" aria-hidden="true"></span>
                    Mantener sesión iniciada
                </label>
            </div>

            <button type="submit" class="auth-btn-primary" id="btn-login">
                Iniciar sesión
            </button>
        </form>

        <p class="auth-switch">
            ¿No tienes cuenta?
            <a href="{{ route('register') }}" class="auth-link">Regístrate gratis</a>
        </p>

    </section>

</main>

<script>
    // Toggle visibilidad de contraseña
    document.querySelectorAll('.auth-toggle-pw').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.target);
            const isText = input.type === 'text';
            input.type = isText ? 'password' : 'text';
            btn.querySelector('.icon-eye').style.display     = isText ? '' : 'none';
            btn.querySelector('.icon-eye-off').style.display = isText ? 'none' : '';
        });
    });
</script>

</body>
</html>
