<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Muestra el formulario de registro.
     */
    public function showRegisterForm()
    {
        // Si ya está autenticado, redirige directamente al listado
        if (Auth::check()) {
            return redirect()->route('rutas.index');
        }

        return view('auth.register');
    }

    /**
     * Valida los datos, crea el usuario y lo autentica automáticamente.
     */
    public function register(Request $request)
    {
        $datos = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'name.required'      => 'El nombre es obligatorio.',
            'name.max'           => 'El nombre no puede tener más de 255 caracteres.',
            'email.required'     => 'El correo electrónico es obligatorio.',
            'email.email'        => 'Ingresa un correo electrónico válido.',
            'email.unique'       => 'Este correo ya está registrado. ¿Quieres iniciar sesión?',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        // Crear el usuario — la contraseña se hashea automáticamente
        // gracias al cast 'hashed' definido en el modelo User.
        $usuario = User::create([
            'name'     => $datos['name'],
            'email'    => $datos['email'],
            'password' => Hash::make($datos['password']),
        ]);

        // Login automático tras el registro
        Auth::login($usuario);

        // Regenerar sesión para prevenir Session Fixation
        $request->session()->regenerate();

        return redirect()->route('rutas.index');
    }
}
