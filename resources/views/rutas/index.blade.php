<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="RutaDirecta GDL — Visualiza las rutas de transporte público de Guadalajara en tiempo real." />
    <title>RutaDirecta GDL — Rutas disponibles</title>

    <!-- Fuente Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    <!-- Estilos locales (public/css/rutas.css) -->
    <link rel="stylesheet" href="/css/rutas.css" />
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
    <div class="avatar" aria-label="Perfil de usuario">U</div>
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
        </div>
        @empty
        <div class="empty-state" id="empty-state" role="status">
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

    <!-- Estado vacío dinámico (búsqueda) -->
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
</body>
</html>
