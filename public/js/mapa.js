/**
 * mapa.js — Lógica del mapa Leaflet para RutaDirecta GDL
 *
 * Contrato de datos — window.PARADAS_DATA
 * ─────────────────────────────────────────────────────────────
 * Inyectado por show.blade.php vía @json() antes de que este
 * script se ejecute. Es un array de objetos con la forma:
 *
 *   {
 *     nombre  : string        — Nombre de la parada
 *     lat     : number        — Latitud  (filtrada: no null, no 0)
 *     lng     : number        — Longitud (filtrada: no null, no 0)
 *     orden   : number|null   — Posición en la ruta (pivot)
 *     eta_min : number|null   — Minutos de ETA (null si ya pasó)
 *     estado  : 'passed' | 'current' | 'future'
 *   }
 *
 * Seguridad:
 *   • PHP → @json escapa <, >, ', " y & (JSON_HEX_* flags).
 *   • JS  → validamos lat/lng con isFinite() antes de usarlos.
 * ─────────────────────────────────────────────────────────────
 */

document.addEventListener('DOMContentLoaded', () => {

    // ════════════════════════════════════════════════════════════
    // 1. GUARDIA: el div#map debe existir en esta vista
    // ════════════════════════════════════════════════════════════
    const contenedor = document.getElementById('map');
    if (!contenedor) return;

    // ════════════════════════════════════════════════════════════
    // 2. DATOS: leer y validar el payload inyectado por PHP
    // ════════════════════════════════════════════════════════════
    const paradasRaw = window.PARADAS_DATA ?? [];

    // Segunda línea de defensa: descartar coords no finitas o cero,
    // aunque el filter() de PHP ya lo garantiza en origen.
    const paradas = paradasRaw.filter(p =>
        isFinite(p.lat) && p.lat !== 0 &&
        isFinite(p.lng) && p.lng !== 0
    );

    if (paradas.length === 0) {
        contenedor.innerHTML =
            '<p style="color:#64748b;text-align:center;padding:2rem">' +
            'Sin coordenadas disponibles para esta ruta.</p>';
        return;
    }

    // ════════════════════════════════════════════════════════════
    // 3. MAPA: inicializar sin centrado fijo (lo hará fitBounds)
    // ════════════════════════════════════════════════════════════
    const map = L.map('map', {
        zoomControl: true,
        scrollWheelZoom: false,  // evita scroll accidental en móvil
    });

    // Capa base de OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(map);

    // ════════════════════════════════════════════════════════════
    // 4. POLYLINE: traza la ruta conectando todas las paradas
    //    en el orden en que llegan (ya vienen ordenadas por PHP).
    // ════════════════════════════════════════════════════════════

    // Extraemos las coordenadas en el mismo orden del array.
    // L.polyline espera un array de [lat, lng].
    const latlngs = paradas.map(p => [p.lat, p.lng]);

    L.polyline(latlngs, {
        color:     '#6366f1',   // índigo — coherente con la paleta de la app
        weight:    4,           // grosor de línea en px
        opacity:   0.85,
        lineJoin:  'round',     // esquinas suavizadas
        lineCap:   'round',
    }).addTo(map);

    // ════════════════════════════════════════════════════════════
    // 5. MARCADORES: un icono personalizado por cada parada,
    //    diferenciado según su estado (passed / current / future)
    // ════════════════════════════════════════════════════════════

    /**
     * Crea un icono DivIcon de Leaflet con un círculo coloreado.
     *
     * @param {string} estado  'passed' | 'current' | 'future'
     * @param {boolean} esTerminal  true para la primera y última parada
     * @returns {L.DivIcon}
     */
    function crearIcono(estado, esTerminal = false) {
        // Paleta de colores por estado
        const colores = {
            passed:  '#94a3b8',   // gris apagado — ya pasó
            current: '#22c55e',   // verde brillante — parada actual
            future:  '#6366f1',   // índigo — próximas paradas
        };

        // Las terminales (inicio/fin de línea) usan ámbar para destacar
        const color      = esTerminal ? '#f59e0b' : (colores[estado] ?? colores.future);
        const tamano     = esTerminal ? 18 : 14;   // px — terminales más grandes
        const glowColor  = estado === 'current' ? 'rgba(34,197,94,0.40)' : 'transparent';

        return L.divIcon({
            className: '',   // sin clase por defecto de Leaflet (evita fondo blanco)
            html: `
                <div style="
                    width:${tamano}px; height:${tamano}px;
                    background:${color};
                    border:3px solid #fff;
                    border-radius:50%;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.45),
                                0 0 0 5px ${glowColor};
                "></div>`,
            // Anclar el centro del círculo al punto geográfico exacto
            iconSize:   [tamano, tamano],
            iconAnchor: [tamano / 2, tamano / 2],
            popupAnchor:[0, -(tamano / 2 + 4)],
        });
    }

    /**
     * Construye el contenido HTML del popup de una parada.
     *
     * @param {object} parada  Objeto del contrato de datos
     * @param {boolean} esTerminal
     * @returns {string} HTML seguro (sin datos de usuario sin escapar)
     */
    function contenidoPopup(parada, esTerminal) {
        // Etiqueta de ETA según el estado
        let etaHtml = '';
        if (parada.estado === 'current') {
            etaHtml = '<p class="popup-eta">⬤ Arriving Now</p>';
        } else if (parada.estado === 'future' && parada.eta_min !== null) {
            etaHtml = `<p class="popup-eta">In ${parada.eta_min} min</p>`;
        } else if (parada.estado === 'passed') {
            etaHtml = '<p class="popup-eta" style="color:#94a3b8">Already departed</p>';
        }

        const badge = esTerminal
            ? '<span style="font-size:0.7rem;color:#f59e0b;font-weight:600">TERMINAL</span><br>'
            : '';

        return `
            <div style="min-width:130px">
                ${badge}
                <p class="popup-nombre">${parada.nombre}</p>
                ${etaHtml}
            </div>`;
    }

    // Iteramos sobre todas las paradas y añadimos cada marcador al mapa
    paradas.forEach((parada, idx) => {
        const esTerminal = (idx === 0 || idx === paradas.length - 1);
        const icono      = crearIcono(parada.estado, esTerminal);

        const marker = L.marker([parada.lat, parada.lng], { icon: icono })
            .addTo(map)
            .bindPopup(contenidoPopup(parada, esTerminal), {
                maxWidth:  200,
                className: 'popup-ruta',   // para estilizar desde mapa.css
            });

        // La parada actual abre su popup automáticamente al cargar
        if (parada.estado === 'current') {
            marker.openPopup();
        }
    });

    // ════════════════════════════════════════════════════════════
    // 6. FITBOUNDS: ajusta el zoom y el centro para que todas
    //    las paradas sean visibles sin ninguna recortada.
    // ════════════════════════════════════════════════════════════

    // L.latLngBounds calcula el rectángulo mínimo que contiene todos los puntos.
    const bounds = L.latLngBounds(latlngs);

    map.fitBounds(bounds, {
        padding: [40, 40],   // margen interior en px para que los marcadores
                             // no queden pegados al borde del contenedor
        maxZoom: 16,         // evita un zoom excesivo si la ruta es corta
    });

    console.info(
        '[RutaDirecta] Mapa listo.',
        `Paradas: ${paradas.length} | Bounds:`, bounds.toBBoxString()
    );

    // Obligamos al mapa a recalcualr su tamaño
    setTimeout(() => {
        map.invalidateSize();
    }, 250);
});

