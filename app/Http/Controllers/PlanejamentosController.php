<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Turma;
use App\Models\PlanejamentoAnual;
use App\Http\Controllers\ProfessoresController;
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
        'rota' => 'Planejamentos/Cadastro'
    ],[
        "nome" => 'Planejamento Anual',
        'endereco' => 'Componentes',
        'rota' => 'Planejamentos/Componentes'
    ]);

    public function index(){
        return view('Planejamentos.index',[
            'submodulos' => self::submodulos
        ]);
    }

    public function componentes($id){
        return view('Planejamentos.componentes',[
            'submodulos' => self::cadastroSubmodulos,
            'id' => $id,
            'Registro' => DB::select("SELECT pa.PLConteudos,t.Periodo FROM planejamentoanual pa INNER JOIN turmas t ON(t.IDPlanejamento = pa.id) WHERE pa.id = $id")[0]
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

    public function save(Request $request){
        try{
            if($request->id){
                $editTurma = array(
                    'IDProfessor' => ProfessoresController::getProfessorByUser(Auth::user()->id),
                    'Aprovado' => $request->Aprovado,
                    'IDDisciplina' => $request->IDDisciplina,
                    'NMPlanejamento' => $request->NMPlanejamento
                );

                if($request->trocaTurma){
                    $mensagem = 'Planejamento Alterado com Sucesso! Como as Turmas foram Alteradas, Espere a Aprovação do Coordenador Pedagógico';
                    if($request->Aprovado){
                        $editTurma['Aprovado'] = 0;
                    }

                    foreach($request->Turma as $t){
                        $Turma = Turma::find($t);
                        $Turma->update(['IDPlanejamento'=>$request->id]);
                    }
                }

                PlanejamentoAnual::create($editTurma);

                $mensagem = 'Planejamento Alterado com Sucesso!';
                $rout = 'Planejamentos/Cadastro';
                $aid = $request->id;
            }else{
                $Planejamento = PlanejamentoAnual::create([
                    'IDProfessor' => ProfessoresController::getProfessorByUser(Auth::user()->id),
                    'Aprovado' => $request->Aprovado,
                    'IDDisciplina' => $request->IDDisciplina,
                    'NMPlanejamento' => $request->NMPlanejamento
                ]);
                    
                foreach($request->Turma as $t){
                    $Turma = Turma::find($t);
                    $Turma->update(['IDPlanejamento'=>$Planejamento->id]);
                }

                $mensagem = 'Planejamento Criado com Sucesso! Agora Crie os Conteúdos e Abordagens de cada Período';
                $rout = 'Planejamentos/Cadastro';
                $aid = $Planejamento->id;
            }
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro:'.$th->getMessage();
            $rout = 'Planejamentos/Novo';
            $aid = '';
            $status = 'error';
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function getPlanejamentos(){
        $orgId = Auth::user()->id_org;
        $SQL = <<<SQL
         SELECT 
            CASE WHEN
            	t.IDPlanejamento
            THEN
            	CONCAT('[', GROUP_CONCAT('"', t.Nome, '"' SEPARATOR ','), ']')
            END AS Turmas,
            pa.id as IDPlanejamento,
            pa.NMPlanejamento
        FROM planejamentoanual pa
        INNER JOIN turmas t ON(pa.id = t.IDPlanejamento)
        INNER JOIN disciplinas d ON(d.id = pa.IDDisciplina)
        INNER JOIN escolas e ON(e.id = t.IDEscola)
        INNER JOIN organizacoes o ON(e.IDOrg = o.id)
        WHERE o.id = $orgId
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
