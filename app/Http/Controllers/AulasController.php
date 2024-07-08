<?php

namespace App\Http\Controllers;

use App\Models\Aulas;
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
            'id' => ''
        ];

        if($id){
            $SQL = <<<SQL
            SELECT
                a.id as IDAula,
                a.DSAula,
                a.STAula,
                d.NMDisciplina,
                a.IDTurma,
                d.id as IDDisciplina,
                a.DSConteudo,
                a.INIAula,
                a.TERAula
            FROM aulas a
            INNER JOIN disciplinas d ON(d.id = a.IDDisciplina)
            INNER JOIN turmas t ON(t.id = a.IDTurma)
            WHERE a.id = $id
            SQL;

            $aula = DB::select($SQL)[0];
            $view['id'] = $id;
            $view['Registro'] = $aula;
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

    public function save(Request $request){
        try{
            if($request->id){
                $rout = 'Aulas/Edit';
                Aulas::find($request->id)->update([
                    'STAula' => 2
                ]);
                $aid = $request->id;
                $status = 'success';
                $mensagem = 'Aula Encerrada com Sucesso!';
            }else{
                $AulaData = $request->all();
                $AulaData['IDProfessor'] = Auth::user()->IDProfissional;
                $Aula = Aulas::create($AulaData);
                $rout = 'Aulas/Edit';
                $aid = $Aula->id;
                $status = 'success';
                $mensagem = 'Aula Iniciada com Sucesso!';
            }
        }catch(\Throwable $th){
            $rout = 'Aulas/Novo';
            $aid = '';
            $status = 'success';
            $mensagem = 'Suspensão Realizada';
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function getAulas(){
        $SQL = <<<SQL
        SELECT
            a.id as IDAula,
            a.DSAula,
            d.NMDisciplina,
            a.DSConteudo,
            a.created_at
        FROM aulas a
        INNER JOIN disciplinas d ON(d.id = a.IDDisciplina)
        SQL;
        $aulas = DB::select($SQL);
        if(count($aulas) > 0){
            foreach($aulas as $a){
                $item = [];
                $item[] = $a->DSAula;
                $item[] = $a->NMDisciplina;
                $item[] = $a->DSConteudo;
                $item[] = 0;
                $item[] = self::data($a->created_at,'d/m/Y');
                $item[] = "<a href=".route('Aulas/Edit',$a->IDAula)." class='btn btn-fr btn-xs'>Abrir Diário</a>";
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
