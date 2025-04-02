<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\Error;
use App\Traits\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use Error, Helpers;

    public function login(Request $request)
    {
        try {
            // Validación con `size:8` para asegurar que el DNI tenga exactamente 8 caracteres
            $credentials = $request->validate([
                'dni' => ['required', 'string', 'size:8'],
                'password' => ['required'],
            ]);

            // Buscar usuario por DNI
            $user = User::with('roles.permissions')
                ->where('dni', $credentials['dni'])
                ->first();

            // Validar usuario y contraseña
            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return response()->json([
                    'error' => 'Las credenciales no coinciden'
                ], 403);
            }

            // Iniciar sesión y regenerar sesión después del login
            Auth::loginUsingId($user->id, true);
            $request->session()->regenerate();

            // Retornar permisos del usuario
            return response()->json($this->extractPermissionsFromUser($user));
        } catch (\Exception $error) {
            return response()->json([
                'error' => 'Ocurrió un error en el servidor',
                'details' => $error->getMessage(),
            ], 500);
        }
    }


    public function verify()
    {
        if (!($id = Auth::id())) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        $user = User::with('roles.permissions')->find($id);

        return response()->json($this->extractPermissionsFromUser($user));
    }

    public function logout(Request $request)
    {
        try {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json(true);
        } catch (\Exception $error) {
            return $this->errorResponse($error);
        }
    }
}
