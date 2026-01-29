<?php

namespace App\Http\Controllers\Api; // <--- CORREGIDO: Añadido \Api

use App\Http\Controllers\Controller; // <--- NUEVO: Importar el padre
use App\Models\Booking;
use App\Models\YogaClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    // CREAR RESERVA
    public function store(Request $request)
    {
        $request->validate([
            'yoga_class_id' => 'required|exists:yoga_classes,id',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Doble seguridad por si falla el middleware
        if (!$user) {
            return response()->json(['message' => 'Debes iniciar sesión'], 401);
        }

        // Usamos DB::transaction para asegurar que todo ocurre a la vez o falla junto
        try {
            return DB::transaction(function () use ($request, $user) {
                
                // 1. Obtener la clase y bloquear la fila para evitar condiciones de carrera (opcional, pero Pro)
                // Usamos lockForUpdate para que si dos personas pulsan a la vez el último sitio, no haya error.
                $yogaClass = YogaClass::where('id', $request->yoga_class_id)->lockForUpdate()->first();

                // 2. Verificar AFORO (Capacity) - ¡IMPORTANTE!
                $currentBookings = Booking::where('yoga_class_id', $yogaClass->id)->count();
                
                if ($currentBookings >= $yogaClass->capacity) {
                    return response()->json(['message' => 'Lo sentimos, la clase está completa.'], 409);
                }

                // 3. Verificar si ya tiene reserva (No duplicar)
                $existingBooking = Booking::where('user_id', $user->id)
                    ->where('yoga_class_id', $yogaClass->id)
                    ->first();

                if ($existingBooking) {
                    return response()->json(['message' => 'Ya tienes plaza en esta clase'], 409);
                }

                // 4. VERIFICAR CRÉDITOS
                if ($user->credits <= 0) {
                    return response()->json([
                        'message' => 'No tienes bonos suficientes. Por favor, recarga tu cuenta.'
                    ], 403);
                }

                // 5. Crear la reserva
                $booking = Booking::create([
                    'user_id' => $user->id,
                    'yoga_class_id' => $yogaClass->id,
                ]);

                // 6. RESTAR EL CRÉDITO
                $user->decrement('credits', $yogaClass->credit_cost);
                
                return response()->json([
                    'message' => 'Reserva realizada con éxito',
                    'cost' => $yogaClass->credit_cost,
                    'credits_left' => $user->fresh()->credits // Obtenemos el saldo actualizado
                ], 201);
            });

        } catch (\Exception $e) {
            // Si algo falla dentro de la transacción (o el return de error), Laravel hace rollback automático
            // Si el error viene de nuestros response()->json, tenemos que devolverlo tal cual.
            if ($e instanceof \Illuminate\Http\Exceptions\HttpResponseException) {
                throw $e;
            }
            
            // Si devolvimos una respuesta JSON dentro de la transacción, DB::transaction la devuelve.
            // Si es un error de respuesta JSON (como el 409 o 403), ya se ha manejado arriba.
            return $e; 
        }
    }

    // LISTAR MIS RESERVAS
    public function index(Request $request)
    {
        // Ordenamos por fecha para ver las próximas primero
        $bookings = $request->user()
            ->bookings()
            ->with('yogaClass')
            ->orderByDesc('created_at') 
            ->get();

        return response()->json($bookings);
    }

    // CANCELAR RESERVA (Y DEVOLVER BONO)
    public function destroy(Request $request, $id)
    {
        $user = Auth::user();

        // 1. Buscamos la reserva y CARGAMOS LA CLASE (con 'yogaClass') para saber el precio
        $booking = Booking::with('yogaClass') 
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $timeDifference = now()->diffInHours($booking->yogaClass->start_time, false); // false para permitir negativos

        // Si la clase ya empezó o falta menos de 2 horas
        if ($timeDifference < 2) { 
            return response()->json([
                'message' => 'No puedes cancelar con menos de 2 horas de antelación.'
            ], 403);
}

        // Usamos transacción para borrar y devolver dinero a la vez
        DB::transaction(function () use ($booking, $user) {
            
            // 2. Guardamos cuánto costaba esa clase antes de borrar nada
            $refundAmount = $booking->yogaClass->credit_cost;  

            // 3. Borrar reserva
            $booking->delete();

            // 4. DEVOLVER LOS CRÉDITOS QUE COSTÓ (1, 4, etc.)
            $user->increment('credits', $refundAmount);
        });

        return response()->json([
            'message' => 'Reserva cancelada y bonos devueltos correctamente',
            'credits_left' => $user->fresh()->credits 
        ]);
    }
}