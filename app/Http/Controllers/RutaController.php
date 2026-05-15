<?php

namespace App\Http\Controllers;

use App\Models\Ruta;
use App\Models\FactorAjuste;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RutaController extends Controller
{
    /**
     * Listado de todas las rutas disponibles.
     * Si el usuario está logueado, pasa los IDs de sus favoritos
     * para que la vista pueda pintar el corazón activo en cada tarjeta.
     */
    public function index()
    {
        $rutas = Ruta::all();

        // IDs de rutas favoritas del usuario — Collection vacía si es guest
        $favoritosIds = Auth::check()
            ? Auth::user()->rutas()->pluck('rutas.id')
            : collect();

        return view('rutas.index', compact('rutas', 'favoritosIds'));
    }

    /**
     * Detalle y seguimiento en vivo de una ruta.
     * Incluye el Motor Predictivo de ETA con factor de ajuste por hora.
     */
    public function show(Ruta $ruta)
    {
        // ── 1. Cargar paradas ordenadas con datos del pivot ──────────────────
        $ruta->load([
            'paradas' => fn($q) => $q->orderBy('ruta_parada.orden_en_ruta'),
        ]);

        // ── 2. Hora actual en Guadalajara (America/Monterrey = UTC-6) ────────
        $ahora      = Carbon::now('America/Monterrey');
        $horaActual = $ahora->format('H:i:s');

        // ── 3. Factor de ajuste activo según la hora actual ──────────────────
        //    Busca el factor cuyo rango horario cubre el momento actual.
        //    Si hay varios activos (ej. lluvia + tráfico), toma el de mayor impacto.
        $factor = FactorAjuste::whereTime('horario_inicio', '<=', $horaActual)
                               ->whereTime('horario_fin',    '>=', $horaActual)
                               ->orderByDesc('impacto_tiempo')
                               ->first();

        // Multiplicador: 1.0 = sin impacto | >1.0 = más lento | <1.0 = más rápido
        $multiplicador     = $factor ? (float) $factor->impacto_tiempo : 1.0;
        $factorDescripcion = $factor?->descripcion;

        // ── 4. Determinar índice de la parada actual ─────────────────────────
        //    Sin GPS real: simulamos que el bus va a la mitad del recorrido.
        //    Cuando integres telemetría, reemplaza esta línea con la lógica real.
        $paradas    = $ruta->paradas->values();
        $total      = $paradas->count();
        $currentIdx = $total > 2 ? (int) floor($total / 2) : 0;

        // ── 5. Motor Predictivo: construir array con ETA por parada ──────────
        //
        //    Fórmula:
        //      ETA_futura[i] = Σ ( tiempo_base[j] × multiplicador )
        //                      para j = currentIdx+1 ... i
        //
        //      Hora_pasada[i] = now() - Σ ( tiempo_base[j] × multiplicador )
        //                               para j = i+1 ... currentIdx
        //
        $paradasConETA            = [];
        $minutosAcumuladosFuturos = 0;

        foreach ($paradas as $idx => $parada) {
            $tiempoBase = (int) ($parada->pivot->tiempo_promedio_entre_paradas ?? 0);

            if ($idx < $currentIdx) {
                // ── PARADA PASADA ────────────────────────────────────────────
                // Calculamos cuántos minutos ajustados hay desde esta parada
                // hasta la actual, sumando los tiempos de cada tramo intermedio.
                $minutosAtras = 0;
                for ($j = $idx + 1; $j <= $currentIdx; $j++) {
                    $t             = (int) ($paradas[$j]->pivot->tiempo_promedio_entre_paradas ?? 1);
                    $minutosAtras += max(1, $t) * $multiplicador;
                }

                $horaSalida = $ahora->copy()->subMinutes((int) round($minutosAtras));

                $paradasConETA[] = [
                    'parada'  => $parada,
                    'estado'  => 'passed',
                    'label'   => 'Departed ' . $horaSalida->format('H:i'),
                    'eta_min' => null,
                ];

            } elseif ($idx === $currentIdx) {
                // ── PARADA ACTUAL ────────────────────────────────────────────
                $paradasConETA[] = [
                    'parada'  => $parada,
                    'estado'  => 'current',
                    'label'   => 'Arriving Now',
                    'eta_min' => 0,
                ];

            } else {
                // ── PARADA FUTURA ────────────────────────────────────────────
                // Acumulamos el tiempo ajustado de cada tramo desde la parada actual.
                $minutosAcumuladosFuturos += max(1, $tiempoBase) * $multiplicador;

                $paradasConETA[] = [
                    'parada'  => $parada,
                    'estado'  => 'future',
                    'label'   => 'In ' . (int) round($minutosAcumuladosFuturos) . ' min',
                    'eta_min' => (int) round($minutosAcumuladosFuturos),
                ];
            }
        }

        // ── 6. ETA a la próxima parada (para el hero card) ──────────────────
        $proximaParada = collect($paradasConETA)->firstWhere('estado', 'future');
        $etaProxima    = $proximaParada ? $proximaParada['eta_min'] : 0;

        // ── 7. ¿Es favorita para el usuario actual? ──────────────────────────
        $esFavorito = Auth::check()
            && Auth::user()->rutas()->where('rutas.id', $ruta->id)->exists();

        return view('rutas.show', compact(
            'ruta',
            'paradasConETA',
            'etaProxima',
            'multiplicador',
            'factorDescripcion',
            'esFavorito'
        ));
    }
}
