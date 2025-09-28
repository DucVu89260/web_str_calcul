<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WindLoadController extends Controller
{
    // Trang tính toán theo TCVN 2737-2023
    public function tcvn27372023()
    {
        return view('admins.windload.tcvn-2737-2023');
    }

    // Trang tính toán theo ASCE 7-10
    public function asce710()
    {
        return view('admins.windload.asce-7-10');
    }
}
