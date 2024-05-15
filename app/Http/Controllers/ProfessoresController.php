<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfessoresController extends Controller
{
    public function index(){
        return view('Professores.index');
    }


    public function acompanhamento(){
        return view('Professores.acompanhamento');
    }

}
