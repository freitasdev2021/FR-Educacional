<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PlanejamentosController extends Controller
{
    public const submodulos = array([
        "nome" => 'Planejamentos',
        'endereco' => 'index',
        'rota' => 'Planejamentos/index'
    ]);

    public const cadastroSubmodulos = array([
        "nome" => 'Cadastro',
        'endereco' => 'Cadastro',
        'Planejamentos/Cadastro'
    ],[
        "nome" => 'Componentes Curriculares',
        'endereco' => 'Componentes',
        'Planejamentos/Componentes'
    ],[
        "nome" => 'Conteudos e Habilidades',
        'endereco' => 'Conteudos',
        'Planejamentos/Conteudos'
    ]);

    public function index(){
        return view('Planejamentos.index',[
            'submodulos' => self::submodulos
        ]);
    }

    public function cadastro($id=null){
        $view = [
            'submodulos' => self::submodulos,
            'id' => '',
            'Disciplinas' => self::getFichaProfessor(Auth::user()->id,'Disciplinas')
        ];

        if($id){
            $view['submodulos'] = self::cadastroSubmodulos;
            $view['id'] = $id;
        }

        return view('Planejamentos.cadastro',$view);
    }

    public function getPlanejamentos(){
        $orgId = Auth::user()->id_org;
        $SQL = <<<SQL
        SELECT 
            CONCAT('[', GROUP_CONCAT('"', t.Nome, '"' SEPARATOR ','), ']') AS Turmas,
            pa.id as IDPlanejamento,
            pa.NMPlanejamento
        FROM planejamentoanual pa
        INNER JOIN turmas t
        INNER JOIN disciplinas d
        INNER JOIN escolas e ON e.id = t.IDEscola
        INNER JOIN organizacoes o ON e.IDOrg = o.id
        WHERE o.id = $orgId
        GROUP BY t.id;
        SQL;

        $Planejamentos = DB::select($SQL);

        if(count($Planejamentos) > 0){
            foreach($Planejamentos as $p){
                $item = [];
                $item[] = $p->NMPlanejamento;
                $item[] = implode(",",json_decode($p->Turmas));
                $item[] = "<a href='".route('Planejamentos/Cadastro',$p->IDPlanejamento)."' class='btn btn-primary btn-xs'>Editar</a>";
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($Planejamentos)),
            "recordsFiltered" => intval(count($Planejamentos)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }
}
