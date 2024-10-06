<?php

namespace App\Http\Controllers;
use App\Http\Controllers\AulasController;
use App\Http\Controllers\AlunosController;
use App\Http\Controllers\EscolasController;
use App\Models\Aluno;
use App\Models\Aulas;
use App\Models\Recuperacao;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RecuperacaoController extends Controller
{
    const submodulos = AulasController::submodulos;

    public function index(){
        return view("Recuperacao.index",[
            "submodulos" => self::submodulos
        ]);
    }

    public function cadastro($id = null){
        $view = [
            "submodulos"=>self::submodulos,
            'id' => '',
            'alunos' => ProfessoresController::getAlunosProfessor(Auth::user()->IDProfissional),
            'disciplinas' => EscolasController::getDisciplinasProfessor(Auth::user()->id)
        ];

        if($id){
            $view['Registros'] = Recuperacao::find($id);
            $view['id'] = $id;
        }

        return view('Recuperacao.cadastro',$view);
    }

    public function save(Request $request){
        try{
            $data = $request->all();

            if($request->id){
                Recuperacao::find($request->id)->update($data);
                $mensagem = "Recuperação Editada com Sucesso!";
                $aid = $request->id;
            }else{
                Recuperacao::create($data);
                $mensagem = "Recuperação cadastrada com Sucesso!";
                $aid = '';
            }
            $status = 'success';
            $rota = 'Aulas/Recuperacao/Novo';
        }catch(\Throwable $th){
            $rota = 'Aulas/Recuperacao/Novo';
            $mensagem = 'Erro '.$th;
            $aid = '';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function getRecuperacoes(){
        $arrAlunos = [];
        if(Auth::user()->tipo == 4){
            foreach(EscolasController::getAlunosEscola() as $a){
                array_push($arrAlunos,$a);
            }
        }else{
            foreach(ProfessoresController::getAlunosProfessor(Auth::user()->IDProfissional) as $a){
                array_push($arrAlunos,$a->id);
            }
        }

        $Alunos = implode(",",$arrAlunos);

        $SQL = <<<SQL
            SELECT m.Nome as Aluno,d.NMDisciplina,r.Estagio,a.id as IDAluno,r.Pontuacao,r.Nota FROM recuperacao r INNER JOIN alunos a ON(a.id = r.IDAluno) INNER JOIN disciplinas d ON(d.id = r.IDDisciplina) INNER JOIN matriculas m ON(a.IDMatricula = m.id) WHERE a.id IN($Alunos)  
        SQL;
        $registros = DB::select($SQL);
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Aluno;
                $item[] = $r->NMDisciplina;
                $item[] = $r->Estagio;
                $item[] = $r->Pontuacao;
                $item[] = $r->Nota;
                $item[] = "<a href='".route('Aulas/Recuperacao/Edit',$r->IDAluno)."' class='btn btn-primary btn-xs'>Abrir</a>";
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($registros)),
            "recordsFiltered" => intval(count($registros)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }
}
