<?php

namespace App\Http\Controllers;

use App\Models\Ruta;
use Illuminate\Http\Request;

class FavoritoController extends Controller
{
    /**
     * Página /favoritos — lista las rutas favoritas del usuario.
     * Protegida con middleware auth en routes/web.php.
     */
    public function index()
    {
        // Carga las rutas favoritas del usuario con eager loading de paradas
        // para no generar N+1 queries si la vista las necesita.
        $rutas = auth()->user()->rutas()->get();

        return view('favoritos.index', compact('rutas'));
    }

    /**
     * Guarda una ruta como favorita del usuario autenticado.
     *
     * Usa syncWithoutDetaching en lugar de attach para que si el registro
     * ya existe (doble clic rápido, retry del navegador, etc.) no lance
     * un error de clave duplicada — simplemente no hace nada.
     *
     * Ruta: POST /favoritos/{ruta}
     * Middleware: auth  (definido en web.php)
     */
    public function store(Ruta $ruta)
    {
        auth()->user()->rutas()->syncWithoutDetaching($ruta->id);

        return response()->json([
            'favorito' => true,
            'message'  => 'Ruta guardada en favoritos.',
        ]);
    }

    /**
     * Elimina una ruta de los favoritos del usuario autenticado.
     *
     * Ruta: DELETE /favoritos/{ruta}
     * Middleware: auth  (definido en web.php)
     */
    public function destroy(Ruta $ruta)
    {
        auth()->user()->rutas()->detach($ruta->id);

        return response()->json([
            'favorito' => false,
            'message'  => 'Ruta eliminada de favoritos.',
        ]);
    }
}
