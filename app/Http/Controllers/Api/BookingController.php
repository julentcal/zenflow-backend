<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking; 
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'yoga_class_id' => 'required|exists:yoga_classes,id',
        ]);

        $booking = Booking::create([
        'user_id' => $request->user()->id, 
        'yoga_class_id' => $validated['yoga_class_id'],
        'status' => 'confirmed'
        ]);

        return response()->json([
            'message' => '¡Reserva creada con éxito!',
            'booking' => $booking
        ], 201);
    }

    public function index(Request $request)
    {
        // 1. Buscamos al usuario actual
        // 2. Pedimos sus reservas
        // 3. Con 'with' cargamos los datos de la clase (para saber el nombre, hora, etc.)
        $bookings = $request->user()->bookings()->with('yogaClass')->get();

        return response()->json($bookings);
    }

    public function destroy(Request $request, $id)
    {
        // 1. Buscamos la reserva, pero ASEGURANDO que sea del usuario logueado
        $booking = $request->user()->bookings()->where('id', $id)->firstOrFail();

        $booking->delete();

        return response()->json(['message' => 'Reserva cancelada']);
    }
}