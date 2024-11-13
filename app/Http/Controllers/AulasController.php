<?php

namespace App\Http\Controllers;

use App\Models\Atividade;
use App\Models\AtividadeAtribuicao;
use App\Models\Aulas;
use App\Models\Disciplina;
use App\Models\Nota;
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
    ],[
        'nome' => 'Recuperação',
        'rota' => 'Aulas/Recuperacao/index',
        'endereco' => 'Recuperacao'
    ],[
        'nome' => 'Diário',
        'rota' => 'Aulas/Diario/index',
        'endereco' => 'Diario'
    ]);
    //
    const submodulosProfessor = array([
        'nome' => 'Aulas',
        'rota' => 'Aulas/index',
        'endereco' => 'index'
    ],[
        'nome' => 'Atividades e Avaliações',
        'rota' => 'Aulas/Atividades/index',
        'endereco' => 'Atividades'
    ],[
        'nome' => 'Recuperação',
        'rota' => 'Aulas/Recuperacao/index',
        'endereco' => 'Recuperacao'
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
    //
    const cadastroAtividades = array([
        'nome' => 'Atividades e Avaliações',
        'rota' => 'Aulas/Atividades/index',
        'endereco' => 'Atividades'
    ],[
        'nome' => 'Lançar Notas',
        'endereco' => 'Correcao',
        'rota' => 'Aulas/Atividades/Correcao'
    ]);
    //
    const cadastroCorrecaoAtividades = array([
        'nome' => 'Lançar Notas',
        'endereco' => 'Atividades',
        'rota' => 'Aulas/Atividades/Correcao'
    ]);
    //LISTAGEM PRINCIPAL
    public function index(){
        $IDEscolas = EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional);
        if(in_array(Auth::user()->tipo,[6,6.5])){
            $submodulos = self::submodulosProfessor;
        }else{
            $submodulos = self::submodulos;
        }

        $view = [
            'submodulos' => $submodulos,
            'Turmas' => ProfessoresController::getTurmasProfessor(Auth::user()->id)
        ];

        if(in_array(Auth::user()->tipo,[4,5,4.5,5.5])){
            $view['Turmas'] = EscolasController::getSelectTurmasEscola($IDEscolas);
        }

        return view('Aulas.index',$view);
    }
    //ATIVIDADES
    public function atividades(){
        $IDEscolas = EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional);
        if(in_array(Auth::user()->tipo,[6,6.5])){
            $submodulos = self::submodulosProfessor;
        }else{
            $submodulos = self::submodulos;
        }

        $view = [
            'submodulos' => $submodulos,
            'Turmas' => ProfessoresController::getTurmasProfessor(Auth::user()->id)
        ];

        if(in_array(Auth::user()->tipo,[4,5,4.5,5.5])){
            $view['Turmas'] = EscolasController::getSelectTurmasEscola($IDEscolas);
        }

        return view('Aulas.atividades',$view);
    }
    //PROFESSORES DE ESCOLAS ESPECIFICAS
    public static function getProfessoresEscola($IDEscolas){
        $orgId = Auth::user()->id_org;
        $AND = " AND a.IDEscola IN(".implode(",",$IDEscolas).")";
        $SQL = <<<SQL
        SELECT 
            p.id AS IDProfessor,
            p.Nome AS Professor,
            us.id as USProfessor
        FROM professores p
        INNER JOIN alocacoes a ON a.IDProfissional = p.id
        INNER JOIN users us ON(us.IDProfissional = p.id)
        INNER JOIN escolas e ON e.id = a.IDEscola
        INNER JOIN organizacoes o ON e.IDOrg = o.id
        WHERE o.id = $orgId $AND AND a.TPProfissional = "PROF"
        GROUP BY p.id, p.Nome, p.Admissao, p.TerminoContrato, p.CEP, p.Rua, p.UF, p.Cidade, p.Bairro, p.Numero;
        SQL;

        return DB::select($SQL);
    }
    //CADASTRO DE AULAS
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
            'id' => ''
        ];

        if(in_array(Auth::user()->tipo,[4,5,4.5,5.5])){
            $view['Professores'] = self::getProfessoresEscola($IDEscolas);
            $view['Turmas'] = EscolasController::getSelectTurmasEscola($IDEscolas);
        }

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
                a.Estagio,
                a.DTAula
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

        if(in_array(Auth::user()->tipo,[6,6.5])){
            $submodulos = self::submodulosProfessor;
        }else{
            $submodulos = self::submodulos;
        }

        $view = [
            'submodulos' => $submodulos,
            'id' => '',
            'Aulas' => Aulas::select('id','DSAula')->where('IDProfessor',Auth::user()->IDProfissional)->get() 
        ];

        if($id){
            //
            $AlunosSQL = <<<SQL
                SELECT 
                    m.Nome AS Aluno,
                    m.id AS IDAluno,
                    CASE WHEN att.IDAluno IS NOT NULL THEN 'checked' ELSE '' END AS Atribuido
                FROM alunos a
                INNER JOIN matriculas m ON m.id = a.IDMatricula
                INNER JOIN turmas t ON a.IDTurma = t.id
                INNER JOIN aulas au ON t.id = au.IDTurma
                LEFT JOIN atividades atv ON au.id = atv.IDAula
                LEFT JOIN atividades_atribuicoes att ON atv.id = att.IDAtividade AND m.id = att.IDAluno
                WHERE t.id = au.IDTurma AND atv.id = $id
                GROUP BY m.Nome, m.id, att.IDAluno;
            SQL;
            //
            $alunosAtividades = DB::select($AlunosSQL);
            //
            $view['id'] = $id;
            $view['submodulos'] = self::cadastroAtividades;
            $view['Alunos'] = $alunosAtividades;
            $view['Registro'] = Atividade::find($id);
        }

        return view('Aulas.cadastroAtividades',$view);
    }
    //
    public function excluirAtividade($id){
        Atividade::find($id)->update(["STDelete"=>1]);
        return redirect()->back();
    }
    //
    public function correcaoAtividades($id){
        //
        $AlunosSQL = <<<SQL
            SELECT 
                m.Nome AS Aluno,
                m.id AS IDAluno,
                CASE WHEN t.TPAvaliacao = 'Conceito' THEN
                    CASE WHEN n.IDAluno IS NOT NULL THEN n.Conceito ELSE '' END
                ELSE
                    CASE WHEN n.IDAluno IS NOT NULL THEN n.Nota ELSE '' END
                END as Pontos
            FROM alunos a
            INNER JOIN matriculas m ON m.id = a.IDMatricula
            INNER JOIN turmas t ON a.IDTurma = t.id
            INNER JOIN aulas au ON t.id = au.IDTurma
            INNER JOIN atividades atv ON au.id = atv.IDAula
            INNER JOIN atividades_atribuicoes att ON atv.id = att.IDAtividade AND m.id = att.IDAluno
            LEFT JOIN notas n ON atv.id = n.IDAtividade AND m.id = n.IDAluno
            WHERE t.id = au.IDTurma AND atv.id = $id
            GROUP BY m.Nome, m.id, n.IDAluno, n.Nota;
        SQL;
        //
        return view('Aulas.correcaoAtividades',[
            'id' => $id,
            'submodulos' => self::cadastroCorrecaoAtividades,
            "Alunos" => DB::select($AlunosSQL)
        ]);
    }
    //
    public function getAulaAlunos(Request $request){
        $SQL = <<<SQL
            SELECT 
                m.Nome AS Aluno,
                m.id AS IDAluno
            FROM alunos a
            INNER JOIN matriculas m ON m.id = a.IDMatricula
            INNER JOIN turmas t ON a.IDTurma = t.id
            INNER JOIN aulas au ON t.id = au.IDTurma
            INNER JOIN frequencia f ON au.id = f.IDAula AND m.id = f.IDAluno
            WHERE t.id = au.IDTurma AND au.id = $request->IDAula
            GROUP BY m.Nome, au.STAula, m.id, f.IDAluno
        SQL;

        $alunosAtividades = DB::select($SQL);

        ob_start();
        foreach($alunosAtividades as $at){
        ?>
            <tr>
                <td><?=$at->Aluno?></td>
                <td><input type="checkbox" value="<?=$at->IDAluno?>" name="Aluno[]"></td>
            </tr>
        <?php
        }
        return ob_get_clean();
    }
    //
    public static function setNota(Request $request){
        try{
            $Notas = [];
            $Aluno = [];
            foreach($request->Pontuacao as $nt){
                if(!is_null($nt)){
                    array_push($Notas,$nt);
                }
            }
            //
            foreach($request->Aluno as $al){
                array_push($Aluno,$al);
            }
            //
            for($i=0;$i<count($Notas);$i++){
                $Pontos[] = [
                    "IDAtividade" => $request->IDAtividade,
                    "IDAluno" => $Aluno[$i]
                ];
                
                if(is_numeric($Notas[$i])){
                    $Pontos[$i]['Nota'] = $Notas[$i];
                }else{
                    $Pontos[$i]['Conceito'] = $Notas[$i]; 
                }
            }

            //dd($Pontos);
            //
            Nota::where('IDAtividade',$request->IDAtividade)->delete();
            foreach($Pontos as $p){
                Nota::create($p);
            }
            //
            $aid = $request->IDAtividade;
            $status = 'success';
            $mensagem = 'Notas Lançadas com Sucesso';
        }catch(\Throwable $th){
            $status = 'error';
            $mensagem = 'Erro ao lançar notas:'. $th->getMessage();
            $aid = $request->IDAtividade;
        }finally{
            return redirect()->route('Aulas/Atividades/Correcao',$aid)->with($status,$mensagem);
        }
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
                if(Auth::user()->tipo == 6){
                    $AulaData['IDProfessor'] = Auth::user()->IDProfissional;
                }else{
                    $AulaData['IDProfessor'] = $request->IDProfessor;
                }
                
                foreach($request->IDDisciplina as $d){
                    $AulaData['IDDisciplina'] = $d;
                    $AulaData['DSAula'] = $request->DSAula." - ".Disciplina::find($d)->NMDisciplina;
                    $Aula = Aulas::create($AulaData);
                    foreach($request->Chamada as $ch){
                        Chamada::create(array(
                            "Presenca" => 0,
                            "IDAluno" => $ch,
                            "IDAula" => $Aula->id
                        ));
                    }
                }
                $rout = 'Aulas/Novo';
                $aid = '';
                $status = 'success';
                $mensagem = 'Aula Iniciada com Sucesso!';
            }
        }catch(\Throwable $th){
            $rout = 'Aulas/Novo';
            $aid = '';
            $status = 'error';
            $mensagem = "Erro ".$th->getMessage();
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }
    //
    public function saveAtividades(Request $request){
        try{
            if($request->id){
                Atividade::find($request->id)->update($request->all());
                if($request->alterarAtt){
                    AtividadeAtribuicao::where('IDAtividade',$request->id)->delete();
                    foreach($request->Aluno as $a){
                        AtividadeAtribuicao::create([
                            "IDAtividade" => $request->id,
                            "IDAluno" => $a
                        ]);
                    }
                }
                //
                $rout = 'Aulas/Atividades/Edit';
                $aid = $request->id;
                $status = 'success';
                $mensagem = 'Atividade Alterada com Sucesso!';
            }else{
                $Atividade = Atividade::create($request->all());
                
                foreach($request->Aluno as $a){
                    AtividadeAtribuicao::create([
                        "IDAtividade" => $Atividade->id,
                        "IDAluno" => $a
                    ]);
                }
                $rout = 'Aulas/Atividades/Correcao';
                $aid = $Atividade->id;
                $status = 'success';
                $mensagem = 'Atividade Cadastrada com Sucesso!';
            }
        }catch(\Throwable $th){
            $rout = 'Aulas/Novo';
            $aid = '';
            $status = 'error';
            $mensagem = 'Erro: '.$th->getMessage();
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }
    //
    public function getAulas(){
        $IDProf = Auth::user()->IDProfissional;

        if(Auth::user()->tipo == 6){
            $WHERE = "WHERE a.IDProfessor = $IDProf";
        }else{
            $WHERE = "WHERE e.id IN(".implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)).")";
        }

        if(isset($_GET['IDTurma'])){
            $WHERE .=" AND a.IDTurma=".$_GET['IDTurma'];
        }

        if(isset($_GET['Estagio'])){
            $WHERE .=" AND a.Estagio='".$_GET['Estagio']."'";
        }

        $SQL = <<<SQL
        SELECT
            a.id as IDAula,
            a.DSAula,
            d.NMDisciplina,
            a.DSConteudo,
            a.DTAula,
            (SELECT COUNT(f2.id) FROM frequencia f2 WHERE f2.IDAula = a.id) as Frequencia
        FROM aulas a
        INNER JOIN turmas t ON(t.id = a.IDTurma)
        INNER JOIN escolas e ON(t.IDEscola = e.id)
        INNER JOIN disciplinas d ON(d.id = a.IDDisciplina)
        LEFT JOIN frequencia f ON(f.IDAula = a.id)
        $WHERE GROUP BY a.id
        SQL;

        $aulas = DB::select($SQL);
        if(count($aulas) > 0){
            foreach($aulas as $a){
                $item = [];
                $item[] = $a->DSAula;
                $item[] = $a->NMDisciplina;
                $item[] = $a->DSConteudo;
                $item[] = $a->Frequencia;
                $item[] = self::data($a->DTAula,'d/m/Y');
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
    public function getAtividades(){
        $IDProf = Auth::user()->IDProfissional;
        if(Auth::user()->tipo == 6){
            $WHERE = "a.IDProfessor = $IDProf";
        }else{
            $WHERE = "t.IDEscola IN(".implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)).")";            
        }

        if(isset($_GET['IDTurma'])){
            $WHERE .=" AND a.IDTurma=".$_GET['IDTurma'];
        }

        if(isset($_GET['Estagio'])){
            $WHERE .=" AND a.Estagio='".$_GET['Estagio']."'";
        }

        $SQL = <<<SQL
        SELECT
            atv.DSAtividade,
            p.Nome as Professor,
            t.Nome as Turma,
            a.DSAula as Aula,
            atv.id as IDAtividade,
            atv.created_at as Aplicada,
            COUNT(n.IDAtividade) as Cumpridos,
            (SELECT COUNT(IDAluno) FROM atividades a2 INNER JOIN atividades_atribuicoes ata ON(a2.id = ata.IDAtividade) WHERE ata.IDAtividade = atv.id AND atv.id = a2.id) as Designados,
            SUM(atv.Pontuacao) as Pontuacao,
            SUM(n.Nota) as Nota
        FROM atividades atv
        LEFT JOIN aulas a ON(a.id = atv.IDAula)
        LEFT JOIN professores p ON(p.id = a.IDProfessor)
        LEFT JOIN turmas t ON(t.id = a.IDProfessor)
        LEFT JOIN notas n ON(atv.id = n.IDAtividade)
        WHERE $WHERE AND atv.STDelete = 0 GROUP BY atv.id
        SQL;
        //dd($SQL);
        $atividades = DB::select($SQL);
        if(count($atividades) > 0){
            foreach($atividades as $a){
                $item = [];
                $item[] = $a->DSAtividade;
                $item[] = $a->Professor;
                $item[] = $a->Turma;
                $item[] = $a->Aula;
                $item[] = self::data($a->Aplicada,'d/m/Y');
                $item[] = "<a href=".route('Aulas/Atividades/Edit',$a->IDAtividade)." class='btn btn-fr btn-xs'>Editar</a> 
                <a href=".route('Aulas/Atividades/Exclusao',$a->IDAtividade)." class='btn btn-danger btn-xs'>Excluir</a>";
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($atividades)),
            "recordsFiltered" => intval(count($atividades)),
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
            WHERE t.id = au.IDTurma AND au.id = $IDAula AND STAluno = 0
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
                $item[] = "<div><input type='checkbox' name='Presenca' onchange='setPresenca({$f->IDAluno}, {$IDAula}, {$f->Presente}, \"{$rota}\")' $checked ></div>";
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
    public function getHtmlAlunosChamada($IDTurma){
        $SQL = <<<SQL
            SELECT 
                m.Nome AS Aluno,
                m.id AS IDAluno
            FROM alunos a
            INNER JOIN matriculas m ON m.id = a.IDMatricula
            INNER JOIN turmas t ON a.IDTurma = t.id
            WHERE t.id = $IDTurma AND STAluno = 0
            GROUP BY m.Nome, m.id
        SQL;
        $frequencia = DB::select($SQL);
        ob_start();
        foreach($frequencia as $f){
            ?>
            <tr>
                <th><?=$f->Aluno?></th>
                <th><input type='checkbox' name='Chamada[]' value='<?=$f->IDAluno?>'></th>
            </tr>
            <?php
        }

        return ob_get_clean();
    }
    //
    public function presencaTodos($IDAula){
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
            WHERE t.id = au.IDTurma AND au.id = $IDAula AND STAluno = 0
            GROUP BY m.Nome, au.STAula, m.id, f.IDAluno
        SQL;
        $frequencia = DB::select($SQL);
        
        foreach($frequencia as $f){
            $Vez = Chamada::where('IDAluno',$f->IDAluno)->where("IDAula",$IDAula)->first();
            if(!$Vez){
                Chamada::create(array(
                    "Presenca" => 0,
                    "IDAluno" => $f->IDAluno,
                    "IDAula" => $IDAula
                ));
            }
        }

        return redirect()->back();
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
