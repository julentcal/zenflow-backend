<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\YogaClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = $request->user()
            ->bookings()
            ->with('yogaClass') 
            ->orderByDesc('created_at')
            ->get();

        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        $request->validate([
            'yoga_class_id' => 'required|exists:yoga_classes,id',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Debes iniciar sesión'], 401);
        }

        try {
            return DB::transaction(function () use ($request, $user) {
                
                $yogaClass = YogaClass::where('id', $request->yoga_class_id)->lockForUpdate()->first();

                $currentBookings = Booking::where('yoga_class_id', $yogaClass->id)->count();
                if ($currentBookings >= $yogaClass->capacity) {
                    return response()->json(['message' => 'Lo sentimos, la clase está completa.'], 409);
                }

                $existingBooking = Booking::where('user_id', $user->id)
                    ->where('yoga_class_id', $yogaClass->id)
                    ->first();

                if ($existingBooking) {
                    return response()->json(['message' => 'Ya tienes plaza en esta clase'], 409);
                }


                if ($user->credits < $yogaClass->credit_cost) {
                    return response()->json([
                        'message' => 'No tienes bonos suficientes. Por favor, recarga tu cuenta.'
                    ], 403);
                }

                $booking = Booking::create([
                    'user_id' => $user->id,
                    'yoga_class_id' => $yogaClass->id,
                    'status' => 'confirmed' 
                ]);

                $user->decrement('credits', $yogaClass->credit_cost);
                
                return response()->json([
                    'message' => 'Reserva realizada con éxito',
                    'booking_id' => $booking->id,
                    'cost' => $yogaClass->credit_cost,
                    'credits_left' => $user->fresh()->credits 
                ], 201);
            });

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al procesar la reserva.',
                'error' => $e->getMessage() 
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        $user = Auth::user();

        $booking = Booking::with('yogaClass')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

 
        $classTime = Carbon::parse($booking->yogaClass->start_time);
        $now = Carbon::now();
        
        $hoursUntilClass = $now->diffInHours($classTime, false);

        if ($hoursUntilClass < 2 && $hoursUntilClass > 0) { 
            return response()->json([
                'message' => 'No puedes cancelar con menos de 2 horas de antelación.'
            ], 403);
        }

        DB::transaction(function () use ($booking, $user) {
            $refundAmount = $booking->yogaClass->credit_cost;  
            $booking->delete();
            $user->increment('credits', $refundAmount);
        });

        return response()->json([
            'message' => 'Reserva cancelada y bonos devueltos correctamente',
            'credits_left' => $user->fresh()->credits 
        ]);
    }
}