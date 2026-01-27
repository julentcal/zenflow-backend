<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

class YogaClass extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'start_time', 'capacity', 'instructor_name'];
}