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
        $rgs = DB::select("SELECT pa.PLConteudos,t.Periodo FROM planejamentoanual pa INNER JOIN turmas t ON(t.IDPlanejamento = pa.id) WHERE pa.id = $id")[0];
        return view('Planejamentos.componentes',[
            'submodulos' => self::cadastroSubmodulos,
            'id' => $id,
            'Registro' => $rgs,
            'Curriculo' => json_decode(json_decode($rgs->PLConteudos))
        ]);
    }

    public function saveComponentes(Request $request){
        try{
            $plan =  PlanejamentoAnual::find($request->IDPlanejamento);
            $plan->update([
                'PLConteudos' => $request->PLConteudos
            ]);
            $retorno['mensagem'] = 'Planejamento Atualizado com Sucesso!';
            $retorno['status'] = 1;
        }catch(\Throwable $th){
            $retorno['status'] = 0;
            $retorno['mensagem'] = $th->getMessage();
        }finally{
            return json_encode($retorno);
        }
    }

    public function getPlanejamento($id){
        $SQL = <<<SQL
        SELECT 
            p.*,
            (SELECT 
                CONCAT(
                    '[',
                    GROUP_CONCAT(
                        CONCAT(
                            '{"Turma":"', t2.Nome, '"',
                            ',"Serie":"', t2.Serie, '"',
                            ',"Escola":"', e.Nome, '"',
                            ',"IDTurma":"', t2.id, '"',
                            ',"Alocada":"', CASE WHEN t2.IDPlanejamento = p.id THEN '1' ELSE '0' END, '"}'
                        ) 
                        SEPARATOR ','
                    ),
                    ']'
                )
            FROM 
                turmas t2 
            LEFT JOIN 
                escolas e ON t2.IDEscola = e.id
            LEFT JOIN 
                turnos tur2 ON(tur2.IDTurma = t2.id)
            WHERE tur2.IDDisciplina = p.IDDisciplina
            ) as Turmas
        FROM 
            planejamentoanual p 
        LEFT JOIN 
            turmas t ON p.id = t.IDPlanejamento
        WHERE 
            p.id = $id
        GROUP BY 
            t.id
        SQL;

        return DB::select($SQL);
    }

    public function getPlanejamentoByTurma($IDTurma){
        $SQL = DB::select("SELECT PLConteudos FROM planejamentoanual WHERE IDTurma = $IDTurma ")[0]->PLConteudos;
    }

    public function cadastro($id=null){
        $view = [
            'submodulos' => self::submodulos,
            'id' => '',
            'Disciplinas' => EscolasController::getDisciplinasProfessor(Auth::user()->id)
        ];

        if($id){
            $rgs = self::getPlanejamento($id)[0];
            $view['submodulos'] = self::cadastroSubmodulos;
            $view['id'] = $id;
            $view['Turmas'] = json_decode($rgs->Turmas);
            $view['Registro'] = $rgs;
        }

        return view('Planejamentos.cadastro',$view);
    }

    public function save(Request $request){
        try{
            if($request->id){

                Turma::where('IDPlanejamento',$request->id)->update(['IDPlanejamento'=>0]);

                foreach($request->Turma as $t){
                    Turma::where('id',$t)->update(['IDPlanejamento'=>$request->id]);
                }

                $mensagem = 'Planejamento Alterado com Sucesso!';
                $rout = 'Planejamentos/Cadastro';
                $aid = $request->id;
            }else{
                $Planejamento = PlanejamentoAnual::create([
                    'IDProfessor' => ProfessoresController::getProfessorByUser(Auth::user()->id),
                    'IDDisciplina' => $request->IDDisciplina,
                    'NMPlanejamento' => $request->NMPlanejamento
                ]);

                foreach($request->Turma as $t){
                    Turma::where('id',$t)->update(['IDPlanejamento'=>$Planejamento->id]);
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
        WHERE o.id = $orgId GROUP BY pa.id
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
