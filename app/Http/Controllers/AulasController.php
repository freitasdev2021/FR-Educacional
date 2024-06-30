<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ProfessoresController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AulasController extends Controller
{

    const submodulos = array([
        'nome' => 'Aulas',
        'rota' => 'Aulas/index',
        'endereco' => 'index'
    ],[
        'nome' => 'Atividades e Avaliações',
        'rota' => 'Aulas/Atividades/index',
        'endereco' => 'Atividades'
    ]);
    //
    const cadastroSubmodulos = array([
        'nome' => 'Aulas',
        'rota' => 'Aulas/Edit',
        'endereco' => 'Edit'
    ],[
        'nome' => 'Lista de Chamada',
        'endereco' => 'Chamada',
        'rota' => 'Aulas/Chamada'
    ]);
    //LISTAGEM PRINCIPAL
    public function index(){
        return view('Aulas.index',[
            'submodulos' => self::submodulos
        ]);
    }
    //ATIVIDADES
    public function atividades(){
        return view('Aulas.atividades',[
            'submodulos' => self::submodulos
        ]);
    }
    //CADASTRO DE AULAS
    public function cadastro($id=null){
        $view = [
            'Turmas' => ProfessoresController::getTurmasProfessor(Auth::user()->id),
            'submodulos' => self::submodulos,
            'id' => '',
        ];

        if($id){
            $view['id'] = $id;
            $view['submodulos'] = self::cadastroSubmodulos;
        }

        return view('Aulas.cadastro',$view);
    }
    //CADASTRO DE ATIVIDADES
    
    public function cadastroAtividades($id=null){
        $view = [
            'submodulos' => self::submodulos,
            'id' => '',
        ];

        if($id){
            $view['id'] = $id;
        }

        return view('Aulas.cadastroAtividades',$view);
    }

    public function getAulas(){
        $SQL = <<<SQL
        SELECT
            a.DSAula,
            d.NMDisciplina,
            a.DSConteudo,
        FROM aulas a
        INNER JOIN disciplinas d ON(d.id = a.IDDisciplina)
        SQL;
        $aulas = DB::select($SQL);
        if(count($aulas) > 0){
            foreach($aulas as $a){
                $item = [];
                $item[] = $a->DSAula;
                $item[] = $a->NMDisciplina;
                $item[] = $a->Conteudo;
                $item[] = 0;
                $item[] = "<a href='".route('Aulas/Edit',$a->IDAula);
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($aulas)),
            "recordsFiltered" => intval(count($aulas)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }
}
