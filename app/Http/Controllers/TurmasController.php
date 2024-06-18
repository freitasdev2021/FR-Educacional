<?php

namespace App\Http\Controllers;
use App\Http\Controllers\EscolasController;
use Illuminate\Http\Request;

class TurmasController extends Controller
{
    public function index(){
        return EscolasController::turmas() ;
    }
}
