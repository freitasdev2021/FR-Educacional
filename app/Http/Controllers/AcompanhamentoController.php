<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AcompanhamentoController extends Controller
{
    public function index(){
        return view('Acompanhamento Semanal.index');
    }
}
