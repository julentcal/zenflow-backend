<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreditController extends Controller
{
    public function buy(Request $request)
    {
        // 1. Validar que nos envían el pack
        $request->validate([
            'pack' => 'required|integer'
        ]);

        // 2. Obtener el usuario autenticado (gracias al token)
        $user = $request->user(); 

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 401);
        }

        // 3. Calcular bonos
        $pack = $request->input('pack');
        $bonos = 0;

        switch ($pack) {
            case 1: $bonos = 1; break;
            case 5: $bonos = 5; break;
            case 10: $bonos = 10; break;
            default:
                return response()->json(['message' => 'Pack no válido'], 400);
        }

        // 4. Sumar los bonos al usuario
        // Asumo que tu columna en base de datos se llama 'credits'
        $user->credits += $bonos;
        $user->save();

        // 5. Responder a React
        return response()->json([
            'message' => '¡Compra realizada con éxito!',
            'new_balance' => $user->credits
        ]);
    }
}