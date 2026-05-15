<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="description" content="RutaDirecta GDL — Tus rutas favoritas guardadas." />
    <title>RutaDirecta GDL — Mis favoritos</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="/css/rutas.css" />
    <link rel="stylesheet" href="/css/favoritos.css" />
</head>
{{-- data-page="favoritos" permite que favoritos.js elimine tarjetas en esta vista --}}
<body data-page="favoritos">

<!-- ── Top Navigation ──────────────────────────────────── -->
<nav class="top-nav" role="navigation" aria-label="Navegación principal">
    <a href="{{ route('rutas.index') }}" class="back-btn" aria-label="Regresar al listado">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
    </a>
    <h1>Mis favoritos</h1>
    <span class="linea-badge" aria-label="Sección">SAVED</span>
    <div class="avatar avatar--auth" aria-label="Usuario">
        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
    </div>
</nav>

<!-- ── Contenido principal ─────────────────────────────── -->
<main class="page" id="main-content">

    <!-- Encabezado de sección -->
    <div class="favoritos-header">
        <p class="section-title">❤️ Mis rutas guardadas</p>
        <span class="favoritos-count" id="fav-count">{{ $rutas->count() }}</span>
    </div>
    <p class="section-subtitle">Toca el corazón para eliminar una ruta de favoritos</p>

    <!-- Lista de rutas favoritas -->
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

            <!-- Botón corazón: siempre activo en esta página -->
            <button class="fav-btn active"
                    id="fav-{{ $ruta->id }}"
                    data-ruta-id="{{ $ruta->id }}"
                    data-favorito="1"
                    aria-label="Quitar de favoritos">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
            </button>
        </div>
        @empty
        {{-- Esta sección se muestra cuando la lista se vacía dinámicamente --}}
        @endforelse

    </div>

    <!-- Estado vacío — visible si no hay favoritos o se eliminan todos -->
    <div class="empty-favoritos"
         id="empty-favoritos"
         role="status"
         aria-live="polite"
         style="{{ $rutas->isEmpty() ? '' : 'display:none' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
        <p>Aún no tienes rutas guardadas</p>
        <a href="{{ route('rutas.index') }}" class="btn-explorar">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            Explorar rutas
        </a>
    </div>

</main>

<script src="/js/favoritos.js"></script>
</body>
</html>
