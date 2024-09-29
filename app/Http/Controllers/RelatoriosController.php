<?php

namespace App\Http\Controllers;
use App\Http\Controllers\EscolasController;
use Illuminate\Http\Request;

class RelatoriosController extends Controller
{

    public const submodulos = EscolasController::submodulos; 

    public function imprimir($Tipo){
        switch($Tipo){
            case 'Ocorrencias':
                $view = 'Escolas.Relatorios.ocorrencias';
            break;
            case 'Transferidos':
                $view = 'Escolas.Relatorios.transferidos';
            break;
            case 'Remanejados':
                $view = 'Escolas.Relatorios.remanejados';
            break;
            case 'Evadidos':
                $view = 'Escolas.Relatorios.evadidos';
            break;
        }
        return view($view,[
            'submodulos' => self::submodulos
        ]);
    }
}
