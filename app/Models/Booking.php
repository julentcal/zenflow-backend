<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    // 1. Permitir asignación masiva
    protected $fillable = ['user_id', 'yoga_class_id', 'status'];

    // 2. Relaciones (El Booking "pertenece" a...)
    
    // Un booking pertenece a un Usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Un booking pertenece a una Clase de Yoga
    public function yogaClass()
    {
        return $this->belongsTo(YogaClass::class);
    }


}