<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApoioController extends Controller
{
    public function index(){
        return view('Apoio.index');
    }
}
