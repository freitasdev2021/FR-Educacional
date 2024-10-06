<?php

namespace App\Http\Controllers;

use App\Models\Disciplina;
use Illuminate\Http\Request;
use App\Models\Professor;
use App\Models\Turno;
use App\Http\Controllers\PedagogosController;
use App\Models\Alocacao;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfessoresController extends Controller
{
    public const submodulos = array([
        "nome" => "Cadastro",
        "endereco" => "index",
        "rota" => "Professores/index"
    ]);

    public const cadastroSubmodulos = array([
        "nome" => 'Cadastro',
        "endereco" => "Edit",
        "rota" => "Professores/Edit"
    ],[
        "nome" => "Turnos",
        "endereco" => "Turnos",
        "rota" => "Professores/Turnos"
    ],[
        "nome" => "Apoio",
        "endereco" => "Apoio",
        "rota" => "Professores/Apoio"
    ]);
        
    public function index(){
        return view('Professores.index',[
            "submodulos" => self::submodulos
        ]);
    }

    public static function getProfessorByUser($IDUser){
        $SQL = "SELECT IDProfissional FROM users u WHERE u.tipo = 6 AND u.id = $IDUser  ";
        return DB::select($SQL)[0]->IDProfissional;
    }

    public static function getEscolaProfessores(){
        if(Auth::user()->tipo == 4){
            $IDEscolas = self::getEscolaDiretor(Auth::user()->id);
        }elseif(Auth::user()->tipo == 5){
            $IDEscolas = implode(",",PedagogosController::getEscolasPedagogo(Auth::user()->IDProfissional));
        }
        $Escolas = array();
        foreach(DB::select("SELECT al.IDProfissional FROM escolas e INNER JOIN alocacoes al ON(al.IDEscola = e.id) WHERE al.TPProfissional = 'PROF' AND al.IDEscola IN($IDEscolas) ") as $p){
            array_push($Escolas,$p->IDProfissional);
        }
        
        return $Escolas;
    }

    public static function getEscolasProfessor($IDProfessor){
        $SQL = "SELECT e.id as IDEscola FROM escolas e INNER JOIN alocacoes a ON(a.IDEscola = e.id) INNER JOIN professores p  ON(p.id = a.IDProfissional) WHERE p.id = $IDProfessor AND a.TPProfissional = 'PROF' ";
        $IDEscolas = [];
        foreach(DB::select($SQL) as $e){
            array_push($IDEscolas,$e->IDEscola);
        }
        return $IDEscolas;
    }

    public static function getDadosEscolasProfessor($IDProfessor){
        $SQL = "SELECT e.id as IDEscola,e.Nome FROM escolas e INNER JOIN alocacoes a ON(a.IDEscola = e.id) INNER JOIN professores p  ON(p.id = a.IDProfissional) WHERE p.id = $IDProfessor AND a.TPProfissional = 'PROF' ";
        $IDEscolas = [];
        foreach(DB::select($SQL) as $e){
            $IDEscolas[] = array(
                "IDEscola" => $e->IDEscola,
                "Nome" => $e->Nome
            );
        }
        return $IDEscolas;
    }

    public static function getAlunosProfessor($IDProfessor){
        $SQL = "SELECT m.Nome as Aluno,a.id,t.Nome as Turma FROM alunos a INNER JOIN matriculas m on(a.IDMatricula = m.id) INNER JOIN turmas t ON(t.id = a.IDTurma) INNER JOIN escolas e ON(t.IDEscola = e.id) INNER JOIN alocacoes al ON(al.IDEscola = e.id) WHERE al.IDProfissional = $IDProfessor";
        return DB::select($SQL);
    }

    public static function getDisciplinasProfessor($IDProfessor){
        $SQL = "SELECT d.NMDisciplina as Disciplina,d.id as IDDisciplina FROM disciplinas d INNER JOIN turnos t ON(d.id = t.IDDisciplina) WHERE t.IDProfessor = $IDProfessor GROUP BY d.id";
        return DB::select($SQL);
    }

    public function getProfessores(){

        if(Auth::user()->tipo == 4){
            $IDEscola = self::getEscolaDiretor(Auth::user()->id);
            $AND = ' AND a.IDEscola='.$IDEscola;
            //dd($IDEscola);
        }else{
            $AND = '';
        }

        $orgId = Auth::user()->id_org;
        $SQL = <<<SQL
        SELECT 
            p.id AS IDProfessor,
            CONCAT('[', GROUP_CONCAT('"', e.Nome, '"' SEPARATOR ','), ']') AS Escolas,
            p.Nome AS Professor,
            p.Admissao,
            p.TerminoContrato,
            p.CEP,
            p.Rua,
            p.UF,
            p.Cidade,
            p.Bairro,
            p.Numero
        FROM Professores p
        INNER JOIN alocacoes a ON a.IDProfissional = p.id
        INNER JOIN escolas e ON e.id = a.IDEscola
        INNER JOIN organizacoes o ON e.IDOrg = o.id
        WHERE o.id = $orgId $AND
        GROUP BY p.id, p.Nome, p.Admissao, p.TerminoContrato, p.CEP, p.Rua, p.UF, p.Cidade, p.Bairro, p.Numero;
        SQL;

        $Professores = DB::select($SQL);

        if(count($Professores) > 0){
            foreach($Professores as $d){
                $item = [];
                $item[] = $d->Professor;
                $item[] = Controller::data($d->Admissao,'d/m/Y');
                $item[] = Controller::data($d->TerminoContrato,'d/m/Y');
                (Auth::user()->tipo == 2) ? $item[] = implode(",",json_decode($d->Escolas)) : '';
                $item[] = $d->Rua.", ".$d->Numero." ".$d->Bairro." ".$d->Cidade."/".$d->UF;
                $item[] = "<a href='".route('Professores/Edit',$d->IDProfessor)."' class='btn btn-primary btn-xs'>Editar</a>";
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($Professores)),
            "recordsFiltered" => intval(count($Professores)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }

    public function getTurnosProfessor($idprofessor){

        if(Auth::user()->tipo == 4){
            $IDEscola = self::getEscolaDiretor(Auth::user()->id);
            $AND = ' AND e.id='.$IDEscola;
            //dd($AND);
        }else{
            $AND = '';
        }

        $SQL = <<<SQL
        SELECT 
            tur.id as IDTurno,
            e.Nome as Escola,
            t.Nome as Turma,
            d.NMDisciplina as Disciplina,
            tur.INITur as Inicio,
            tur.TERTur as Termino
        FROM turnos tur 
        INNER JOIN turmas t ON(t.id = tur.IDTurma)
        INNER JOIN escolas e ON(e.id = t.IDEscola)
        INNER JOIN disciplinas d ON(d.id = tur.IDDisciplina)
        INNER JOIN professores p ON(p.id = tur.IDProfessor)
        WHERE p.id = $idprofessor $AND
        SQL;
        //
        $Turnos = DB::select($SQL);

        if(count($Turnos) > 0){
            foreach($Turnos as $t){
                $item = [];
                (Auth::user()->tipo == 2) ? $item[] = $t->Escola : '';
                $item[] = $t->Turma;
                $item[] = $t->Disciplina;
                $item[] = Controller::data($t->Inicio,'d/m/Y');
                $item[] = Controller::data($t->Termino,'d/m/Y');
                $item[] = "<a href='".route('Professores/Turnos/Edit',['idprofessor' => $idprofessor,'id' => $t->IDTurno])."' class='btn btn-primary btn-xs'>Editar</a>";
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($Turnos)),
            "recordsFiltered" => intval(count($Turnos)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
        //
    }

    public function cadastroTurnoProfessor($idprofessor,$id=null){
        $idorg = Auth::user()->id_org;
        $view = array(
            "submodulos" => array([
                "nome" => 'Cadastro',
                "endereco" => "Edit",
                "rota" => "Professores/Edit"
            ],[
                "nome" => "Turnos",
                "endereco" => "Turnos",
                "rota" => "Professores/Turnos"
            ],[
                "nome" => "Apoio",
                "endereco" => "Apoio",
                "rota" => "Professores/Apoio"
            ]),
            'IDProfessor' => $idprofessor,
            'Escolas' => DB::select("SELECT e.Nome as Escola, e.id as IDEscola FROM escolas e INNER JOIN alocacoes a ON(e.id = a.IDEscola) INNER JOIN professores p ON(p.id = a.IDProfissional) WHERE p.id = $idprofessor "),
            'Disciplinas' => DB::select("SELECT 
                d.NMDisciplina as Disciplina,
                d.id as IDDisciplina
            FROM disciplinas d
            INNER JOIN alocacoes_disciplinas ad ON(ad.IDDisciplina = d.id)
            WHERE ad.IDEscola IN(".implode(',',self::getEscolasProfessor($idprofessor)).") "),
            "Turmas" => DB::select("SELECT t.Nome as Turma, t.id as IDTurma,t.Serie,e.Nome as Escola FROM turmas t INNER JOIN escolas e ON(e.id = t.IDEscola) INNER JOIN alocacoes a ON(a.IDEscola = e.id) INNER JOIN professores p ON(p.id = a.IDProfissional) INNER JOIN organizacoes o ON(e.IDOrg = o.id) WHERE o.id = $idorg GROUP BY t.id ")
        );

        if($id){

            $SQL = <<<SQL
            SELECT tur.id as IDTurno,
                e.id as IDEscola,
                e.Nome as Escola,
                t.Nome as Turma,
                d.NMDisciplina as Disciplina,
                tur.INITur as Inicio,
                DiaSemana,
                tur.TERTur as Termino
                FROM turnos tur 
                INNER JOIN turmas t ON(t.id = tur.IDTurma)
                INNER JOIN escolas e ON(e.id = t.IDEscola)
                INNER JOIN disciplinas d ON(d.id = tur.IDDisciplina)
                INNER JOIN professores p ON(p.id = tur.IDProfessor)
            WHERE p.id = $idprofessor AND tur.id = $id
            SQL;

            $view['Registro'] = DB::select($SQL)[0];
            $IDEscola = $view['Registro']->IDEscola;
            $view['Disciplinas'] = DB::select("SELECT 
                d.NMDisciplina as Disciplina,
                d.id as IDDisciplina
            FROM disciplinas d
            INNER JOIN alocacoes_disciplinas ad ON(ad.IDDisciplina = d.id)
            WHERE ad.IDEscola = $IDEscola");

            $view[1]['nome'] = 'Cadastro Turno';
            $view[1]['endereco'] = 'Cadastro Turno';
            $view[1]['rota'] = 'Profesores/Turnos/Edit';
        }

        return view('Professores.cadastroTurnos',$view);
    }

    public function cadastro($id=null){
        $orgId = Auth::user()->id_org;
        $view = [
            "submodulos" => self::submodulos,
            'id' => '',
            'EscolasRegistradas' => Controller::array_associative_unique(DB::select("SELECT e.id as IDEscola,e.Nome FROM escolas e INNER JOIN organizacoes o ON(e.IDOrg = o.id) WHERE o.id = $orgId ORDER BY e.Nome"))
        ];

        if($id){
            
            $sqlScola = <<<SQL
            SELECT 
                e.id AS IDEscola,
                e.Nome,
                a.INITurno,
                a.TERTurno,
                CASE WHEN (SELECT COUNT(IDEscola) FROM alocacoes WHERE IDProfissional = $id AND IDEscola = e.id AND TPProfissional = 'PROF') THEN 1 ELSE 0 END AS Alocado
            FROM escolas e 
            LEFT JOIN alocacoes a ON e.id = a.IDEscola
            LEFT JOIN professores p ON p.id = a.IDProfissional
            INNER JOIN organizacoes o ON e.IDOrg = o.id  
            WHERE o.id = $orgId
            GROUP BY e.Nome ORDER BY e.Nome
            SQL;

           
            $escolasUm = Controller::array_associative_unique(DB::select($sqlScola));
            $SQL = <<<SQL
            SELECT 
                p.*,
                u.STAcesso,
                u.id as IDUser
            FROM Professores p
            LEFT JOIN alocacoes a ON(a.IDProfissional = p.id)
            LEFT JOIN escolas e ON(e.id = a.IDEscola)
            INNER JOIN organizacoes o ON(e.IDOrg = o.id)
            INNER JOIN users u ON(u.IDProfissional = p.id)
            WHERE o.id = $orgId AND p.id = $id AND u.tipo = 6
            SQL;

            $Professor = DB::select($SQL);


            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = "Professores/Edit";
            //
            array_push($view['submodulos'],[
                "nome" => "Turnos",
                "endereco" => "Turnos",
                "rota" => "Professores/Turnos"
            ]);

            array_push($view['submodulos'],[
                "nome" => "Apoio",
                "endereco" => "Apoio",
                "rota" => "Professores/Apoio"
            ]);
            //
            $view['id'] = $id;
            $view['Registro'] = $Professor[0];
            $view['EscolasRegistradas'] = $escolasUm;
        }

        return view('Professores.cadastro',$view);

    }

    public function bloquearAcesso($IDUser,$STAcesso){
        if($STAcesso == 1){
            User::find($IDUser)->update(["STAcesso"=>0]);
        }else{
            User::find($IDUser)->update(["STAcesso"=>1]);
        }
    }

    public function Turnos($idprofessor){
        $view = array(
            "submodulos" => array([
                "nome" => 'Cadastro',
                "endereco" => "Edit",
                "rota" => "Professores/Edit"
            ],[
                "nome" => "Turnos",
                "endereco" => "Turnos",
                "rota" => "Professores/Turnos"
            ],[
                "nome" => "Apoio",
                "endereco" => "Apoio",
                "rota" => "Professores/Apoio"
            ]),
            'IDProfessor' => $idprofessor
        );

        return view('Professores.turnos',$view);
    }

    public static function getTurmasProfessor($id){
        $SQL = <<<SQL
        SELECT 
            t.id as IDTurma,
            t.Nome as Turma,
            t.Serie,
            e.Nome as Escola
        FROM turnos tn
        INNER JOIN turmas t ON(tn.IDTurma = t.id)
        INNER JOIN alocacoes al ON(t.IDEscola = al.IDEscola)
        INNER JOIN escolas e ON(al.IDEscola = e.id)
        INNER JOIN professores p ON(p.id = tn.IDProfessor)
        INNER JOIN users us ON(us.IDProfissional = p.id)
        INNER JOIN disciplinas d ON(d.id = tn.IDDisciplina)
        WHERE us.id = $id GROUP BY tn.INITur,tn.TERTur,tn.DiaSemana
        SQL;

        return DB::select($SQL);

    }

    public function getDisciplinasTurmaProfessor($IDTurma){
        $id = Auth::user()->id;
        $SQL = <<<SQL
        SELECT
            d.id as IDDisciplina,
            d.NMDisciplina as Disciplina
        FROM turnos tn
        INNER JOIN turmas t ON(tn.IDTurma = t.id)
        INNER JOIN alocacoes al ON(t.IDEscola = al.IDEscola)
        INNER JOIN escolas e ON(al.IDEscola = e.id)
        INNER JOIN professores p ON(p.id = tn.IDProfessor)
        INNER JOIN users us ON(us.IDProfissional = p.id)
        INNER JOIN disciplinas d ON(d.id = tn.IDDisciplina)
        INNER JOIN turnos tur ON(tur.IDDisciplina = d.id)
        WHERE us.id = $id AND t.id = $IDTurma GROUP BY d.id
        SQL;
        ob_start();
        foreach(DB::select($SQL) as $tp){
        ?>
        <option value="">Selecione</option>
        <option value="<?=$tp->IDDisciplina?>"><?=$tp->Disciplina?></option>
        <?php
        }
        return ob_get_clean();
    }

    public function saveTurno(Request $request){
        try{
            if($request->id){
                // dd(Turno::find($request->id)->first());
                Turno::find($request->id)->update($request->all());
                $aid = array(
                    'idprofessor' => $request->IDProfessor,
                    'id' => $request->id
                );
                $rout = "Professores/Turnos/Edit";
            }else{
                Turno::create($request->all());
                $aid = array(
                    'idprofessor' => $request->IDProfessor,
                    'id' => ''
                );
                $rout = "Professores/Turnos/Novo";
            }
            $mensagem = "Turno Salvo com Sucesso";
            $status = "success";
        }catch(\Throwable $th){
            $mensagem = "Erro ao Salvar o Turno ".$th;
            $status = 'error';
            $aid = array(
                'idprofessor' => $request->IDProfessor,
                'id' => ''
            );
            $rout = "Professores/Turnos/Novo";
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function save(Request $request){
        try{
            $aid = '';
            $dir = $request->all();
            $dir['CEP'] = preg_replace('/\D/', '', $request->CEP);
            $dir['Celular'] = preg_replace('/\D/', '', $request->Celular);
            if($request->id){
                $Professor = Professor::find($request->id);
                $Professor->update($dir);
                $rout = 'Professores/Edit';
                $aid = $request->id;
                if($request->credenciais){
                    $mensagem = 'Salvamento Feito com Sucesso! as Novas Credenciais de Login foram Enviadas no Email Cadastrado';
                    $Usuario = User::where('IDProfissional',$request->id)->where('id_org',Auth::user()->id_org);
                    $Usuario->update([
                        'name' => $request->Nome,
                        'email' => $request->Email,
                        'tipo' => 6,
                        'password' => Hash::make(rand(100000,999999))
                    ]);
                }else{
                    $mensagem = 'Salvamento Feito com Sucesso!';
                }
                ////
                if($request->alocacoes){
                    $alocacoes = [];
                    $iniTurno = [];
                    $terTurno = [];
                    $escolas = $request->Escola;
                    $Alocacao = Alocacao::where('IDProfissional',$request->id)->where('TPProfissional','PROF');
                    $Alocacao->delete();

                    foreach($request->INITur as $it){
                        if(!is_null($it)){
                            array_push($iniTurno,$it);
                        }
                    }
    
                    foreach($request->TERTur as $tt){
                        if(!is_null($tt)){
                            array_push($terTurno,$tt);
                        }
                    }
    
                    for($i=0; $i<count($escolas);$i++){
                        $alocacoes[] = [
                            'IDEscola' => $escolas[$i],
                            "INITurno" => $iniTurno[$i],
                            "TERTurno" => $terTurno[$i],
                            "IDProfissional" => $request->id,
                            'TPProfissional' => 'PROF'
                        ];
                    }
                    
                    foreach($alocacoes as $al){
                        Alocacao::create($al);
                    }
                }
                ///
            }else{
                $alocacoes = [];

                $profId = Professor::create($dir);


                function filterNull($var){
                    return !is_null($var);
                }

                $iniTurno = [];
                $terTurno = [];
                $escolas = $request->Escola;
                
                foreach($request->INITur as $it){
                    if(!is_null($it)){
                        array_push($iniTurno,$it);
                    }
                }

                foreach($request->TERTur as $tt){
                    if(!is_null($tt)){
                        array_push($terTurno,$tt);
                    }
                }

                for($i=0; $i<count($escolas);$i++){
                    $alocacoes[] = [
                        'IDEscola' => $escolas[$i],
                        'IDProfissional' => $profId->id,
                        "INITurno" => $iniTurno[$i],
                        "TERTurno" => $terTurno[$i],
                        'TPProfissional' => 'PROF'
                    ];
                }
                
                foreach($alocacoes as $al){
                    Alocacao::create($al);
                }

                User::create([
                    'name' => $request->Nome,
                    'email' => $request->Email,
                    'tipo' => 6,
                    'password' => Hash::make(rand(100000,999999)),
                    'IDProfissional' => $profId->id,
                    'id_org' => Auth::user()->id_org
                ]);
                $rout = 'Professores/Novo';
                $mensagem = 'Salvamento Feito com Sucesso! as Credenciais de Login foram Enviadas no Email Cadastrado';
            }
            $status = 'success';
        }catch(\Throwable $th){
            $rout = 'Professores/Novo';
            $status = 'error';
            $mensagem = "Erro ao Salvar a Escola: ".$th->getMessage();
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

}
