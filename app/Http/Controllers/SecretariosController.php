<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SecretariosController extends Controller
{
    public function index(){
        return view('Secretarios.index');
    }
}
