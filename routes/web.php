<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\RutaController;

// ── Raíz: redirige al listado ────────────────────────────────
Route::get('/', fn() => redirect()->route('rutas.index'));

// ── Rutas de autenticación ───────────────────────────────────
Route::get('/login',    [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login',   [LoginController::class, 'login']);
Route::post('/logout',  [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register',[RegisterController::class, 'register']);

// ── Rutas del dominio ────────────────────────────────────────
Route::get('/rutas',         [RutaController::class, 'index'])->name('rutas.index');
Route::get('/rutas/{ruta}',  [RutaController::class, 'show'])->name('rutas.show');

// ── Favoritos (requieren sesión activa) ──────────────────────
// Si el usuario no está logueado, Laravel redirige automáticamente a /login.
Route::middleware('auth')->group(function () {
    Route::get('/favoritos',           [FavoritoController::class, 'index'])->name('favoritos.index');
    Route::post('/favoritos/{ruta}',   [FavoritoController::class, 'store'])->name('favoritos.store');
    Route::delete('/favoritos/{ruta}', [FavoritoController::class, 'destroy'])->name('favoritos.destroy');
});
