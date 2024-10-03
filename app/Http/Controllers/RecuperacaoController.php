<?php

namespace App\Http\Controllers;
use App\Http\Controllers\AulasController;
use App\Http\Controllers\AlunosController;
use App\Models\Aluno;
use App\Models\Aulas;
use App\Models\Recuperacao;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RecuperacaoController extends Controller
{
    const submodulos = AulasController::submodulos;

    public index(){
        return view("Recuperacao.index",[
            "submodulos" => self::submodulos
        ]);
    }
}
