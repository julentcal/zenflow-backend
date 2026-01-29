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
        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Sesión cerrada correctamente']);
        }

        return response()->json(['message' => 'No había sesión activa o el token era inválido'], 200);
    }
}