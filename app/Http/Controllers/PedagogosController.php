<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PedagogosController extends Controller
{
    public function index(){
        return view('Pedagogos.index');
    }
}
