<?php

namespace App\Http\Controllers;

use App\Models\Atividade;
use App\Models\AtividadeAtribuicao;
use App\Http\Controllers\AulasController;
use App\Http\Controllers\CalendarioController;
use App\Models\Aulas;
use App\Models\Disciplina;
use App\Models\Nota;
use App\Models\Chamada;
use Illuminate\Http\Request;
use App\Http\Controllers\ProfessoresController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AvaliacoesController extends Controller
{
    public const submodulos = AulasController::submodulos;
    //
    public const submodulosProfessor = AulasController::submodulosProfessor;
    //
    public const cadastroSubmodulos = AulasController::cadastroSubmodulos;
    //
    public const cadastroAvaliacoes = AulasController::cadastroAtividades;
    //
    public  const cadastroCorrecaoAvaliacoes = AulasController::cadastroCorrecaoAtividades;
    //
    //LISTAGEM PRINCIPAL
    public function index(){
        //CONSULTA E FILTROS
        $IDProf = Auth::user()->IDProfissional;

        if(Auth::user()->tipo == 6){
            $WHERE = "WHERE a.IDProfessor = $IDProf AND a.TPConteudo=1";
        }else{
            $WHERE = "WHERE e.id IN(".implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)).") AND a.TPConteudo=1";
        }

        if(isset($_GET['IDTurma']) && !empty($_GET['IDTurma'])){
            $WHERE .=" AND a.IDTurma='".$_GET['IDTurma']."'";
        }

        if(isset($_GET['Estagio']) && !empty($_GET['Estagio'])){
            $WHERE .=" AND a.Estagio='".$_GET['Estagio']."'";
        }

        if(isset($_GET['Professor']) && !empty($_GET['Professor'])){
            $WHERE .=" AND p.id='".$_GET['Professor']."'";
        }

        $WHERE .= " AND DATE_FORMAT(a.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')";

        //dd($WHERE);

        $SQL = <<<SQL
        SELECT 
            a.DSConteudo,
            a.DTAula,
            a.Estagio,
            p.Nome as Professor,
            p.id as IDProfessor,
            a.Hash,
            t.id as IDTurma,
            CONCAT('[', GROUP_CONCAT('"', d.id, '"' SEPARATOR ','), ']') AS Disciplinas,
            CONCAT(
                '[',
                GROUP_CONCAT(
                    DISTINCT
                    '{'
                    ,'"Disciplina":"', d.NMDisciplina, '"'
                    ,',"Conteudo":"', a.DSConteudo, '"'
                    ,',"Data":"', a.DTAula, '"'
                    ,',"Frequencia":"', (SELECT COUNT(f2.id) FROM frequencia f2 WHERE f2.IDAula = a.id), '"'
                    ,'}'
                    SEPARATOR ','
                ),
                ']'
            ) AS CTAula
        FROM aulas a 
        INNER JOIN professores p ON(p.id = a.IDProfessor)
        INNER JOIN turmas t ON(t.id = a.IDTurma) 
        INNER JOIN escolas e ON(t.IDEscola = e.id) 
        INNER JOIN disciplinas d ON(d.id = a.IDDisciplina) 
        LEFT JOIN frequencia f ON(f.IDAula = a.id) 
        $WHERE GROUP BY a.Hash ORDER BY DTAula ASC
        SQL;
        //dd($SQL);
        $aulas = DB::select($SQL);
        
        //RESTO DA VIEW
        $IDEscolas = EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional);
        if(in_array(Auth::user()->tipo,[6,6.5])){
            $submodulos = self::submodulosProfessor;
        }else{
            $submodulos = self::submodulos;
        }

        $view = [
            'submodulos' => $submodulos,
            'Turmas' => ProfessoresController::getTurmasProfessor(Auth::user()->id),
            'Aulas' => $aulas
        ];

        if(in_array(Auth::user()->tipo,[4,5,4.5,5.5])){
            $view['Professores'] = AulasController::getProfessoresEscola($IDEscolas);
            $view['Turmas'] = EscolasController::getSelectTurmasEscola($IDEscolas);
        }

        if(in_array(Auth::user()->tipo,[4,5,4.5,5.5])){
            $view['Turmas'] = EscolasController::getSelectTurmasEscola($IDEscolas);
        }

        if(in_array(Auth::user()->tipo,[2,2.5])){
            $view['Professores'] = ProfessoresController::getProfessoresRede(Auth::user()->id_org);
        }

        return view('Avaliacoes.index',$view);
    }
    //
    public function save(Request $request){
        try{
            $Notas = [];
            $Aluno = [];
            //dd($request->all());
            $Hash = self::randomHash();
            $AulaData = $request->all();
            $AulaData['Hash'] = $Hash;
            
            if(Auth::user()->tipo == 6){
                $AulaData['IDProfessor'] = Auth::user()->IDProfissional;
            }else{
                $AulaData['IDProfessor'] = $request->IDProfessor;
            }
            $Aula = Aulas::create($AulaData);
            //
            $Atividade = Atividade::create([
                "IDAula" => $Aula->id,
                "TPConteudo" => $request->DSConteudo,
                "Pontuacao" => $request->Pontuacao
            ]);
            //
            foreach($request->Nota as $nt){
                if(!is_null($nt)){
                    array_push($Notas,$nt);
                }
            }
            //
            foreach($request->Aluno as $al){
                if(!is_null($al)){
                    array_push($Aluno,$al);
                }

                AtividadeAtribuicao::create([
                    "IDAtividade" => $Atividade->id,
                    "IDAluno" => $al
                ]);
            }
            //
            for($i=0;$i<count($Notas);$i++){
                $Pontos[] = [
                    "IDAtividade" => $Atividade->id,
                    "IDAluno" => $Aluno[$i]
                ];
                
                if(is_numeric($Notas[$i])){
                    $Pontos[$i]['Nota'] = $Notas[$i];
                }else{
                    $Pontos[$i]['Conceito'] = $Notas[$i]; 
                }
            }
            //
            Nota::where('IDAtividade',$Atividade->id)->delete();
            foreach($Pontos as $p){
                Chamada::create(array(
                    "Presenca" => 0,
                    "IDAluno" => $p['IDAluno'],
                    "IDAula" => $Aula->id
                ));
                Nota::create($p);
            }
            //
            $rout = 'Aulas/Avaliacoes/Novo';
            $aid = '';
            $status = 'success';
            $mensagem = 'Avaliação Lançada com Sucesso!';
        }catch(\Throwable $th){
            $rout = 'Aulas/Avaliacoes/Novo';
            $aid = '';
            $status = 'error';
            $mensagem = "Erro ".$th->getMessage();
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }
    //
    public function cadastro($id=null){
        $IDEscolas = EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional);
        //dd(self::getProfessoresEscola($IDEscolas));
        if(in_array(Auth::user()->tipo,[6,6.5])){
            $submodulos = self::submodulosProfessor;
        }else{
            $submodulos = self::submodulos;
        }

        //
        
        //

        $view = [
            'Turmas' => ProfessoresController::getTurmasProfessor(Auth::user()->id),
            'submodulos' => $submodulos,
            'id' => '',
            'Datas'=> CalendarioController::diasLetivos()
        ];

        if(in_array(Auth::user()->tipo,[4,5,4.5,5.5])){
            $view['Professores'] = AulasController::getProfessoresEscola($IDEscolas);
            $view['Turmas'] = EscolasController::getSelectTurmasEscola($IDEscolas);
        }

        if($id){
            $SQL = <<<SQL
            SELECT
                a.id as IDAula,
                a.STAula,
                d.NMDisciplina,
                a.IDTurma,
                d.id as IDDisciplina,
                a.DSConteudo,
                a.Estagio,
                a.DTAula,
                a.id as IDTurma,
                us.id as IDProfessor
            FROM aulas a
            INNER JOIN users us ON(us.IDProfissional = a.IDProfessor)
            INNER JOIN disciplinas d ON(d.id = a.IDDisciplina)
            INNER JOIN turmas t ON(t.id = a.IDTurma)
            WHERE a.id = $id
            SQL;

            //dd($SQL);

            $aula = Aulas::find($id);
            $view['id'] = $id;
            $view['Registro'] = $aula;
            $view['submodulos'] = self::cadastroSubmodulos;
        }

        return view('Avaliacoes.cadastro',$view);
    }
}
