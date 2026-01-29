<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\YogaClass; 

class YogaClassController extends Controller
{
    public function index(){ 
    $userId = auth('sanctum')->id() ?? 0;

    return YogaClass::withCount('bookings')
                ->withExists(['bookings as is_booked_by_user' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }])
                ->orderBy('start_time', 'asc')
                ->get();
    }
}


