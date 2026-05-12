/* =========================================================
   RutaDirecta GDL — JavaScript de rutas
   ========================================================= */

document.addEventListener('DOMContentLoaded', () => {

    /* ── Búsqueda en tiempo real ─────────────────────────── */
    const searchInput = document.getElementById('search-rutas');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase().trim();
            const cards  = document.querySelectorAll('.ruta-card');

            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.closest('.ruta-item').style.display =
                    text.includes(query) ? '' : 'none';
            });

            // Mostrar estado vacío si no hay resultados
            const emptyState = document.getElementById('empty-state');
            if (emptyState) {
                const visible = [...cards].some(c => c.closest('.ruta-item').style.display !== 'none');
                emptyState.style.display = visible ? 'none' : 'block';
            }
        });
    }

    /* ── Animación de entrada en cards ──────────────────── */
    const rutaCards = document.querySelectorAll('.ruta-card, .pill, .route-hero');
    rutaCards.forEach((el, i) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(16px)';
        el.style.transition = `opacity .4s ease ${i * 60}ms, transform .4s ease ${i * 60}ms`;
        requestAnimationFrame(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        });
    });

    /* ── Live countdown — sincronizado con el ETA del backend ── */
    const liveTime = document.getElementById('next-time');
    if (liveTime) {
        // Lee el valor en segundos inyectado por Blade (data-eta-seconds="N")
        let seconds = parseInt(liveTime.dataset.etaSeconds ?? '0', 10);

        // Si el backend ya indica "Ahora" (0 min) no hay nada que contar
        if (seconds <= 0) {
            liveTime.textContent = 'Ahora';
        } else {
            let timerId;

            // Primer .next-time de la timeline (fila "Next Stop")
            const nextTimeTimeline = document.querySelector('.next-time');

            const updateCountdown = () => {
                if (seconds <= 0) {
                    liveTime.textContent = 'Ahora';
                    if (nextTimeTimeline) nextTimeTimeline.textContent = 'Ahora';
                    clearInterval(timerId);
                    return;
                }

                const min = Math.floor(seconds / 60);
                const sec = seconds % 60;

                // Muestra "X min" mientras quede más de 1 minuto,
                // luego cambia a "X seg" para los últimos 59 segundos
                const display = min > 0 ? `${min} min` : `${sec} seg`;
                liveTime.textContent = display;

                // Espeja el mismo valor en el .next-time de la timeline
                if (nextTimeTimeline) nextTimeTimeline.textContent = display;

                seconds--;
            };

            // Ejecutar inmediatamente y luego cada segundo
            updateCountdown();
            timerId = setInterval(updateCountdown, 1000);
        }
    }

    /* ── Botones QR / NFC ────────────────────────────────── */
    const btnQR = document.getElementById('btn-qr');
    if (btnQR) {
        btnQR.addEventListener('click', () => {
            showToast('📱 Generando código QR...');
        });
    }

    const btnNFC = document.getElementById('btn-nfc');
    if (btnNFC) {
        btnNFC.addEventListener('click', () => {
            showToast('📡 Activando NFC Pay...');
        });
    }

    /* ── Toast helper ────────────────────────────────────── */
    function showToast(msg) {
        const existing = document.querySelector('.rd-toast');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.className = 'rd-toast';
        toast.textContent = msg;
        Object.assign(toast.style, {
            position:     'fixed',
            bottom:       '30px',
            left:         '50%',
            transform:    'translateX(-50%) translateY(20px)',
            background:   '#1E293B',
            color:        '#fff',
            padding:      '12px 24px',
            borderRadius: '50px',
            fontSize:     '14px',
            fontWeight:   '600',
            fontFamily:   'Inter, sans-serif',
            boxShadow:    '0 8px 24px rgba(0,0,0,.25)',
            zIndex:       '9999',
            opacity:      '0',
            transition:   'all .3s ease',
            whiteSpace:   'nowrap',
        });
        document.body.appendChild(toast);

        requestAnimationFrame(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(-50%) translateY(0)';
        });

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(-50%) translateY(10px)';
            setTimeout(() => toast.remove(), 350);
        }, 2500);
    }

    /* ── Botón Top Up ────────────────────────────────────── */
    const topUpBtn = document.querySelector('.top-up-btn');
    if (topUpBtn) {
        topUpBtn.addEventListener('click', () => {
            showToast('💳 Abriendo recarga Mi Movilidad...');
        });
    }

});
