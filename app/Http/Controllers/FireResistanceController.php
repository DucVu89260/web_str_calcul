<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FireResistanceController extends Controller
{
    public function index()
    {
        return view('admins.fire_resistance.index');
    }
}
