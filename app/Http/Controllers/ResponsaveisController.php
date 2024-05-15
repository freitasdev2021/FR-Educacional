<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResponsaveisController extends Controller
{
    public function index(){
        return view('Responsaveis.index');
    }
}
