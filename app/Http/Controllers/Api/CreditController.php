<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreditController extends Controller
{
    public function buy(Request $request)
    {
        $request->validate([
            'pack' => 'required|integer'
        ]);

        $user = $request->user(); 

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 401);
        }

        $pack = $request->input('pack');
        $bonos = 0;

        switch ($pack) {
            case 1: $bonos = 1; break;
            case 5: $bonos = 5; break;
            case 10: $bonos = 10; break;
            default:
                return response()->json(['message' => 'Pack no válido'], 400);
        }

        $user->credits += $bonos;
        $user->save();

        return response()->json([
            'message' => '¡Compra realizada con éxito!',
            'new_balance' => $user->credits
        ]);
    }
}