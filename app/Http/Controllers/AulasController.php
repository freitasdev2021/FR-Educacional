<?php

namespace App\Http\Controllers;

use App\Models\Aulas;
use App\Models\Chamada;
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
        'endereco' => 'Presenca',
        'rota' => 'Aulas/Presenca'
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

    //
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
    //
    public function chamada($IDAula){
        return view('Aulas.chamada',[
            'submodulos' => self::cadastroSubmodulos,
            'id' => $IDAula
        ]);
    }
    //
    public function getAulaPresenca($IDAula){
        $SQL = <<<SQL
            SELECT 
                m.Nome AS Aluno,
                au.STAula,
                m.id AS IDAluno,
                CASE WHEN f.IDAluno IS NOT NULL THEN 1 ELSE 0 END AS Presente
            FROM alunos a
            INNER JOIN matriculas m ON m.id = a.IDMatricula
            INNER JOIN turmas t ON a.IDTurma = t.id
            INNER JOIN aulas au ON t.id = au.IDTurma
            LEFT JOIN frequencia f ON au.id = f.IDAula AND m.id = f.IDAluno
            WHERE t.id = au.IDTurma AND au.id = $IDAula
            GROUP BY m.Nome, au.STAula, m.id, f.IDAluno
        SQL;
        $frequencia = DB::select($SQL);
        $rota = route('Aulas/setPresenca');
        if(count($frequencia) > 0){
            foreach($frequencia as $f){
                $checked = '';
                $disabled = '';
                if($f->Presente){
                    $checked = 'checked';
                }

                if($f->STAula == 2){
                    $disabled = 'disabled';
                }   

                $item = [];
                $item[] = $f->Aluno;
                $item[] = "<input type='checkbox' name='Presenca' $disabled onchange='setPresenca({$f->IDAluno}, {$IDAula}, {$f->Presente}, \"{$rota}\")' $checked >";
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($frequencia)),
            "recordsFiltered" => intval(count($frequencia)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }
    //
    public function setPresenca(Request $request){
        try{
            $Vez = Chamada::where('IDAluno',$request->IDAluno)->where("IDAula",$request->IDAula)->first();
            if($Vez){
                Chamada::where('IDAluno',$request->IDAluno)->where("IDAula",$request->IDAula)->delete();
            }else{
                Chamada::create($request->all());
            }
            $retorno = "";
        }catch(\Throwable $th){
            $retorno = $th->getMessage();
        }finally{
            return $retorno;
        }
    }
    //
}
