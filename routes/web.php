<?php

use App\Http\Controllers\RutaController;

// Página de inicio → redirige al listado de rutas
Route::get('/', fn() => redirect()->route('rutas.index'));

// Listado de rutas
Route::get('/rutas', [RutaController::class, 'index'])->name('rutas.index');

// Detalle de una ruta
Route::get('/rutas/{ruta}', [RutaController::class, 'show'])->name('rutas.show');
