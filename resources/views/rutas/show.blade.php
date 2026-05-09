<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="RutaDirecta GDL — Detalle y seguimiento en vivo de la ruta {{ $ruta->numero_ruta }}." />
    <title>RutaDirecta GDL — Ruta {{ $ruta->numero_ruta }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="/css/rutas.css" />
</head>
<body>

<!-- ── Top Navigation ──────────────────────────────────── -->
<nav class="top-nav" role="navigation" aria-label="Navegación">
    <a href="{{ route('rutas.index') }}" class="back-btn" aria-label="Regresar a rutas">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
    </a>
    <h1>Route Details</h1>
    <span class="linea-badge" aria-label="Número de ruta">{{ $ruta->numero_ruta }}</span>
    <div class="avatar" aria-label="Perfil">U</div>
</nav>

<!-- ── Contenido principal ─────────────────────────────── -->
<main class="page" id="main-content">

    <!-- ── Hero: Ruta actual ────────────────────────────── -->
    <section class="route-hero" aria-label="Información de ruta actual">
        <div class="hero-top">
            <div>
                <p class="hero-label">Current Route</p>
                <p class="hero-route-name">{{ $ruta->empresa_operadora }}</p>
            </div>
            <div class="hero-bus-icon" aria-hidden="true">
                <!-- Bus SVG -->
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17 20H7v1a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-1H3V8c0-2.21 3.58-4 8-4s8 1.79 8 4v12h-1v1a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1v-1zm2-9H5v4h14V11zm0 5H5v2h14v-2zM7.5 14a1 1 0 1 1 0-2 1 1 0 0 1 0 2zm9 0a1 1 0 1 1 0-2 1 1 0 0 1 0 2zM5 8v2h14V8H5z"/>
                </svg>
            </div>
        </div>

        <div class="hero-bottom">
            <div>
                <p class="hero-destination-label">Destination</p>
                @if($ruta->paradas->isNotEmpty())
                    <p class="hero-destination">{{ $ruta->paradas->last()->nombre }}</p>
                @else
                    <p class="hero-destination">{{ $ruta->nombre_comun }}</p>
                @endif
            </div>
            <div>
                <p class="hero-next-label">Next Stop</p>
                <p class="hero-next-time" id="next-time">4 min</p>
            </div>
        </div>
    </section>

    <!-- ── Info Pills ────────────────────────────────────── -->
    <div class="info-pills" role="list" aria-label="Estado del servicio">

        <div class="pill" role="listitem">
            <span class="pill-icon" aria-hidden="true">👥</span>
            <div>
                <p class="pill-label">Occupancy</p>
                <p class="pill-value">Low (15%)</p>
            </div>
        </div>

        <div class="pill" role="listitem">
            <span class="pill-icon" aria-hidden="true">✅</span>
            <div>
                <p class="pill-label">Status</p>
                <p class="pill-value on-time">On Time</p>
            </div>
        </div>

        <div class="pill" role="listitem">
            <span class="pill-icon" aria-hidden="true">🌡️</span>
            <div>
                <p class="pill-label">Temp</p>
                <p class="pill-value">22°C</p>
            </div>
        </div>

    </div>

    <!-- ── Live Timeline ─────────────────────────────────── -->
    <section class="timeline-card" aria-label="Línea de tiempo en vivo">

        <div class="timeline-header">
            <h2 class="timeline-title">Live Timeline</h2>
            <div class="live-badge" aria-live="polite">
                <span class="live-dot" aria-hidden="true"></span>
                LIVE TRACKING
            </div>
        </div>

        <ol class="timeline" aria-label="Paradas de la ruta">

            @php
                $paradas     = $ruta->paradas->sortBy(fn($p) => $p->pivot->orden_en_ruta)->values();
                $totalParadas = $paradas->count();
                // Simulamos que la parada actual es la del medio (o la primera si hay pocas)
                $currentIdx  = $totalParadas > 2 ? (int)floor($totalParadas / 2) : 0;
            @endphp

            @forelse ($paradas as $idx => $parada)
                @php
                    $tiempoDesde = $parada->pivot->tiempo_promedio_entre_paradas ?? 0;

                    if ($idx < $currentIdx) {
                        $stopClass = 'passed';
                        $hora = now()->subMinutes(($currentIdx - $idx) * max(1, $tiempoDesde))->format('H:i');
                        $label = "Departed $hora";
                    } elseif ($idx === $currentIdx) {
                        $stopClass = 'current';
                        $hora = '';
                        $label = '';
                    } else {
                        $stopClass = 'future';
                        $hora = '';
                        $minutosRestantes = ($idx - $currentIdx) * max(1, $tiempoDesde);
                        $label = "In $minutosRestantes min";
                    }
                @endphp

                <li class="stop {{ $stopClass }}"
                    aria-label="{{ $parada->nombre }}{{ $stopClass === 'current' ? ' — Parada actual' : '' }}">

                    <div class="stop-dot" aria-hidden="true"></div>

                    @if ($stopClass === 'passed')
                        <p class="stop-time">{{ $label }}</p>
                        <p class="stop-name">{{ $parada->nombre }}</p>

                    @elseif ($stopClass === 'current')
                        <div class="current-bubble">
                            <div class="current-bubble-top">
                                <span class="current-label">Current Stop</span>
                                <span class="arriving-label">Arriving Now</span>
                            </div>
                            <p class="current-stop-name">{{ $parada->nombre }}</p>
                        </div>

                    @else
                        <div class="next-stop-row">
                            <div>
                                <p class="next-label">Next Stop</p>
                                <p class="next-name">{{ $parada->nombre }}</p>
                            </div>
                            <span class="next-time">{{ $label }}</span>
                        </div>
                    @endif

                </li>
            @empty
                <li class="stop future">
                    <div class="stop-dot" aria-hidden="true"></div>
                    <p class="stop-name" style="color:#94a3b8">Sin paradas registradas</p>
                </li>
            @endforelse

        </ol>

    </section>

    <!-- ── Payment Card ──────────────────────────────────── -->
    <section class="payment-card" aria-label="Opciones de pago">

        <div class="movilidad-row">
            <div class="movilidad-icon" aria-hidden="true">
                <span>MI<br>Movilidad</span>
            </div>
            <div class="movilidad-info">
                <p class="movilidad-label">Mi Movilidad Card</p>
                <p class="movilidad-balance">$42.50 MXN</p>
            </div>
            <button class="top-up-btn" id="btn-topup" aria-label="Recargar tarjeta Mi Movilidad">
                Top Up
            </button>
        </div>

        <div class="payment-actions">
            <button class="btn-qr" id="btn-qr" aria-label="Pagar con código QR">
                <!-- QR icon -->
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                    <rect x="5" y="5" width="3" height="3" fill="currentColor"/>
                    <rect x="16" y="5" width="3" height="3" fill="currentColor"/>
                    <rect x="16" y="16" width="3" height="3" fill="currentColor"/>
                    <rect x="5" y="16" width="3" height="3" fill="currentColor"/>
                </svg>
                QR Ticket
            </button>
            <button class="btn-nfc" id="btn-nfc" aria-label="Pagar con NFC">
                <!-- NFC icon -->
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 12a8 8 0 0 0-8-8"/><path d="M20 12a8 8 0 0 1-8 8"/>
                    <path d="M16 12a4 4 0 0 0-4-4"/><path d="M16 12a4 4 0 0 1-4 4"/>
                    <circle cx="12" cy="12" r="1" fill="currentColor"/>
                </svg>
                NFC Pay
            </button>
        </div>

    </section>

</main>

<script src="/js/rutas.js"></script>
</body>
</html>
