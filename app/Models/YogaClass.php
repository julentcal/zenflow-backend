<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;
use App\Models\Booking;

class YogaClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_time',
        'name',
        'instructor_name',
        'duration_minutes',
        'capacity',
        'description'
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}