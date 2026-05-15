<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function showLoginForm()
    {
        // Si ya está autenticado, redirige directamente al listado
        if (Auth::check()) {
            return redirect()->route('rutas.index');
        }

        return view('auth.login');
    }

    /**
     * Procesa las credenciales y autentica al usuario.
     */
    public function login(Request $request)
    {
        $credenciales = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'El correo electrónico es obligatorio.',
            'email.email'       => 'Ingresa un correo electrónico válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        // remember: mantiene la sesión activa aunque cierre el navegador
        $remember = $request->boolean('remember');

        if (Auth::attempt($credenciales, $remember)) {
            // Regenerar el ID de sesión para prevenir Session Fixation
            $request->session()->regenerate();

            return redirect()->route('rutas.index');
        }

        // Credenciales incorrectas — regresa al formulario con error
        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Las credenciales no coinciden con nuestros registros.']);
    }

    /**
     * Cierra la sesión del usuario autenticado.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidar y regenerar el token CSRF para evitar reutilización
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('rutas.index');
    }
}
