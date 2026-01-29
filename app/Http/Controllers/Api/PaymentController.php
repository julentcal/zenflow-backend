<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function simulateBuyingCredits(Request $request)
    {
        $request->validate([
            'pack' => 'required|in:1,5,10' 
        ]);

        $creditsToAdd = (int) $request->pack;
        $user = Auth::user();

        $user->increment('credits', $creditsToAdd);

        return response()->json([
            'message' => '¡Compra realizada con éxito!',
            'new_balance' => $user->credits
        ]);
    }
}