<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DiretoresController extends Controller
{
    public function index(){
        return view('Diretores.index');
    }
}
