<?php

namespace App\Http\Controllers;

use App\Models\Ruta;
use Illuminate\Http\Request;

class RutaController extends Controller
{
    /**
     * Listado de todas las rutas disponibles.
     */
    public function index()
    {
        $rutas = Ruta::all();
        return view('rutas.index', compact('rutas'));
    }

    /**
     * Detalle y seguimiento en vivo de una ruta.
     */
    public function show(Ruta $ruta)
    {
        // Cargamos las paradas con el pivot (orden + tiempo)
        $ruta->load(['paradas' => function ($query) {
            $query->orderBy('ruta_parada.orden_en_ruta');
        }]);

        return view('rutas.show', compact('ruta'));
    }
}
