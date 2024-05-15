<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CardapioController extends Controller
{
    public function index(){
        return view('Cardapio.index');
    }
}
