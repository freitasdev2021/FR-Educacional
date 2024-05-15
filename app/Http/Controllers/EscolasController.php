<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EscolasController extends Controller
{
    public function index(){
        return view('Escolas.index');
    }
}
