<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Credenciales incorrectas (Email o contraseña mal)'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // Opcional: Borrar tokens viejos para que solo haya una sesión activa
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Hola ' . $user->name,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        // 1. Obtenemos el usuario de la petición
        $user = $request->user();

        // 2. Comprobamos si hay usuario y si tiene un token actual
        if ($user && $user->currentAccessToken()) {
            // Borramos el token que se usó para esta petición
            $user->currentAccessToken()->delete();
            return response()->json(['message' => 'Sesión cerrada correctamente']);
        }

        // Si llegamos aquí es que no había token o ya estaba borrado
        return response()->json(['message' => 'No había sesión activa o el token era inválido'], 200);
    }
}