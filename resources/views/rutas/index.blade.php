<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="description" content="RutaDirecta GDL — Visualiza las rutas de transporte público de Guadalajara en tiempo real." />
    <title>RutaDirecta GDL — Rutas disponibles</title>

    <!-- Fuente Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    <!-- Estilos locales -->
    <link rel="stylesheet" href="/css/rutas.css" />
    <link rel="stylesheet" href="/css/favoritos.css" />
</head>
<body>

<!-- ── Top Navigation ──────────────────────────────────── -->
<nav class="top-nav" role="navigation" aria-label="Navegación principal">
    <button class="back-btn" onclick="history.back()" aria-label="Regresar">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
    </button>
    <h1>Rutas disponibles</h1>
    <span class="linea-badge" aria-label="Línea activa">LÍNEA 3</span>

    {{-- Enlace a "Saved" — solo si hay sesión activa --}}
    @auth
    <a href="{{ route('favoritos.index') }}" class="nav-saved-link" aria-label="Mis rutas favoritas">
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
        Saved
    </a>
    @endauth

    {{-- Avatar: inicial del nombre si está logueado, enlace a login si es guest --}}
    @auth
    <div class="avatar avatar--auth" aria-label="Usuario: {{ Auth::user()->name }}">
        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
    </div>
    @else
    <a href="{{ route('login') }}" class="avatar avatar--guest" aria-label="Iniciar sesión">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
            <polyline points="10 17 15 12 10 7"/>
            <line x1="15" y1="12" x2="3" y2="12"/>
        </svg>
    </a>
    @endauth
</nav>

<!-- ── Contenido principal ─────────────────────────────── -->
<main class="page" id="main-content">

    <!-- Encabezado de sección -->
    <div>
        <p class="section-title">🚌 Explorar rutas</p>
        <p class="section-subtitle">Selecciona una ruta para ver su seguimiento en vivo</p>
    </div>

    <!-- Barra de búsqueda -->
    <div class="search-bar" role="search">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <input type="text"
               id="search-rutas"
               placeholder="Buscar por nombre o número de ruta..."
               aria-label="Buscar rutas"
               autocomplete="off" />
    </div>

    <!-- Lista de rutas -->
    <div class="rutas-grid" id="rutas-grid" role="list">

        @forelse ($rutas as $ruta)
        <div class="ruta-item" role="listitem">

            <a class="ruta-card"
               href="{{ route('rutas.show', $ruta->id) }}"
               aria-label="Ver ruta {{ $ruta->numero_ruta }} — {{ $ruta->nombre_comun }}">

                <!-- Número de ruta -->
                <div class="ruta-badge" aria-hidden="true">
                    {{ $ruta->numero_ruta }}
                </div>

                <!-- Información -->
                <div class="ruta-info">
                    <p class="ruta-nombre">{{ $ruta->nombre_comun }}</p>
                    <p class="ruta-empresa">{{ $ruta->empresa_operadora }}</p>
                </div>

                <!-- Tarifa -->
                <div class="ruta-tarifa" aria-label="Tarifa">
                    ${{ number_format($ruta->tarifa, 2) }}
                </div>

                <!-- Flecha -->
                <div class="ruta-arrow" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </div>
            </a>

            {{-- Botón corazón: solo para usuarios logueados --}}
            @auth
            @php $esFav = $favoritosIds->contains($ruta->id); @endphp
            <button class="fav-btn {{ $esFav ? 'active' : '' }}"
                    id="fav-{{ $ruta->id }}"
                    data-ruta-id="{{ $ruta->id }}"
                    data-favorito="{{ $esFav ? '1' : '0' }}"
                    aria-label="{{ $esFav ? 'Quitar de favoritos' : 'Guardar en favoritos' }}">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
            </button>
            @endauth

        </div>
        @empty
        <div class="empty-state" role="status">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="1" y="3" width="15" height="13" rx="2"/>
                <path d="M16 8h4l3 3v5h-7V8z"/>
                <circle cx="5.5" cy="18.5" r="2.5"/>
                <circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
            <p>No hay rutas disponibles por el momento.</p>
        </div>
        @endforelse

    </div>

    <!-- Estado vacío dinámico (búsqueda sin resultados) -->
    <div class="empty-state" id="empty-state" style="display:none;" role="status" aria-live="polite">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <p>No se encontraron rutas con ese término.</p>
    </div>

</main>

<!-- Script local (public/js/rutas.js) -->
<script src="/js/rutas.js"></script>
<script src="/js/favoritos.js"></script>
</body>
</html>
