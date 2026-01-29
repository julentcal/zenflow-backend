<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    // Simula una compra exitosa
    public function simulateBuyingCredits(Request $request)
    {
        $request->validate([
            'pack' => 'required|in:1,5,10' // Solo permitimos packs de 1, 5 o 10
        ]);

        $creditsToAdd = (int) $request->pack;
        $user = Auth::user();

        // Sumamos los créditos
        $user->increment('credits', $creditsToAdd);

        return response()->json([
            'message' => '¡Compra realizada con éxito!',
            'new_balance' => $user->credits
        ]);
    }
}