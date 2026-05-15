/**
 * favoritos.js — Lógica del sistema de favoritos para RutaDirecta GDL
 *
 * Responsabilidades:
 *  - Interceptar clics en cualquier .fav-btn de la página.
 *  - Enviar POST o DELETE a /favoritos/{id} con el token CSRF.
 *  - Actualizar el ícono de corazón sin recargar la página (optimistic UI).
 *  - En la página /favoritos: eliminar la tarjeta del DOM cuando se desmarca.
 *
 * Dependencia: <meta name="csrf-token" content="..."> en el <head>.
 */

// ── 1. Token CSRF (inyectado por Laravel en el meta tag) ──────────────
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content;

// ── 2. Delegación de eventos ──────────────────────────────────────────
//    Un solo listener en el documento captura todos los botones .fav-btn,
//    incluyendo los que se inserten dinámicamente en el futuro.
document.addEventListener('click', e => {
    const btn = e.target.closest('.fav-btn');
    if (!btn) return;

    // Evita que el clic se propague al <a> de la tarjeta
    e.preventDefault();
    e.stopPropagation();

    const rutaId = btn.dataset.rutaId;
    if (rutaId) toggleFavorito(btn, rutaId);
});

// ── 3. Función principal: toggle ──────────────────────────────────────
/**
 * Envía la petición al servidor y actualiza el botón.
 *
 * @param {HTMLElement} btn     El botón .fav-btn clicado
 * @param {string}      rutaId  ID de la ruta en la base de datos
 */
async function toggleFavorito(btn, rutaId) {
    const esFavorito = btn.dataset.favorito === '1';
    const method     = esFavorito ? 'DELETE' : 'POST';

    // ── Optimistic UI: actualiza el ícono antes de la respuesta ──────
    //    Si el servidor falla, lo revertimos en el catch.
    actualizarBoton(btn, !esFavorito);

    try {
        const respuesta = await fetch(`/favoritos/${rutaId}`, {
            method,
            headers: {
                'X-CSRF-TOKEN':  CSRF_TOKEN,
                'Accept':        'application/json',
                'Content-Type':  'application/json',
            },
        });

        // fetch NO lanza error en 4xx/5xx — lo verificamos manualmente
        if (!respuesta.ok) {
            actualizarBoton(btn, esFavorito);  // revertir

            // Si el servidor rechaza por falta de auth, redirige al login
            if (respuesta.status === 401 || respuesta.status === 403) {
                window.location.href = '/login';
            }
            return;
        }

        const data = await respuesta.json();

        // Confirmar estado final con el valor real del servidor
        actualizarBoton(btn, data.favorito);

        // ── Lógica especial en la página /favoritos ───────────────────
        //    Si el usuario desmarca una ruta en esa vista, la tarjeta
        //    desaparece con una animación suave.
        if (!data.favorito && document.body.dataset.page === 'favoritos') {
            eliminarTarjeta(btn);
        }

    } catch (err) {
        // Error de red — revertir el cambio optimista
        actualizarBoton(btn, esFavorito);
        console.error('[RutaDirecta] Error al actualizar favorito:', err.message);
    }
}

// ── 4. Helpers ────────────────────────────────────────────────────────

/**
 * Actualiza el atributo data-favorito, la clase .active y aplica
 * la micro-animación de pulso al activar.
 */
function actualizarBoton(btn, esFavorito) {
    btn.dataset.favorito = esFavorito ? '1' : '0';
    btn.setAttribute('aria-label', esFavorito ? 'Quitar de favoritos' : 'Guardar en favoritos');
    btn.classList.toggle('active', esFavorito);

    // Dispara la animación solo al activar (no al quitar)
    if (esFavorito) {
        btn.classList.remove('pulse');
        // Fuerza reflow para reiniciar la animación si se hace clic rápido
        void btn.offsetWidth;
        btn.classList.add('pulse');
        btn.addEventListener('animationend', () => btn.classList.remove('pulse'), { once: true });
    }
}

/**
 * Anima y elimina la tarjeta del DOM en la página /favoritos.
 * Si la cuadrícula queda vacía, muestra el estado vacío.
 */
function eliminarTarjeta(btn) {
    const tarjeta = btn.closest('.ruta-item');
    if (!tarjeta) return;

    // Fade-out rápido
    tarjeta.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
    tarjeta.style.opacity    = '0';
    tarjeta.style.transform  = 'scale(0.96)';

    setTimeout(() => {
        tarjeta.remove();

        // ¿Quedó vacía la cuadrícula?
        const grid = document.getElementById('rutas-grid');
        if (grid && grid.children.length === 0) {
            const emptyState = document.getElementById('empty-favoritos');
            if (emptyState) emptyState.style.display = 'block';

            // Actualiza el contador en el nav
            const contador = document.getElementById('fav-count');
            if (contador) contador.textContent = '0';
        }

        // Actualiza el contador
        const contador = document.getElementById('fav-count');
        if (contador) {
            const actual = parseInt(contador.textContent, 10);
            if (!isNaN(actual) && actual > 0) {
                contador.textContent = actual - 1;
            }
        }
    }, 300);
}
