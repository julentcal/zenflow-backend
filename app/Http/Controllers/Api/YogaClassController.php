<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\YogaClass; 
use Illuminate\Http\Request;

class YogaClassController extends Controller
{
    public function index(){ 
        return YogaClass::orderBy('start_time', 'asc')->get(); 
        return YogaClass::where('start_time', '>=', now())
                        ->orderBy('start_time', 'asc')
                        ->get();   
        }
}