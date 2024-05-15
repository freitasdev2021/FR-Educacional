<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuxiliaresController extends Controller
{
    public function index(){
        return view('Auxiliares.index');
    }
}
