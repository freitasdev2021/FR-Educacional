<?php

namespace App\Http\Controllers;

use App\Models\Disciplina;
use Illuminate\Http\Request;
use App\Models\Professor;
use App\Models\Turno;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Http\Controllers\PedagogosController;
use App\Models\Alocacao;
use App\Models\User;
use App\Models\Contrato;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfessoresController extends Controller
{
    public const submodulos = array([
        "nome" => "Cadastro",
        "endereco" => "index",
        "rota" => "Professores/index"
    ],[
        "nome" => "Contratos",
        "endereco" => "Contratos",
        "rota" => "Professores/Contratos"
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

    public function contratos(){
        return view('Professores.contratos',[
            "submodulos" => self::submodulos
        ]);
    }

    //PROFESSORES DE ESCOLAS ESPECIFICAS
    public static function getProfessoresRede($IDRede){
        
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
        WHERE o.id = $IDRede AND a.TPProfissional = "PROF"
        GROUP BY p.id, p.Nome, p.Admissao, p.TerminoContrato, p.CEP, p.Rua, p.UF, p.Cidade, p.Bairro, p.Numero;
        SQL;

        return DB::select($SQL);
    }

    public function getContratos(){
        $IDOrg = Auth::user()->id_org;
        $SQL = <<<SQL
            SELECT
                c.id,
                c.STContrato,
                p.Nome as Professor,
                c.Nome as Contrato,
                c.Salario,
                c.Inicio,
                c.Termino
            FROM contratos c
            INNER JOIN professores p ON(p.id = c.IDProfessor)
            INNER JOIN users u ON(p.IDUser = u.id)
            WHERE u.id_org = $IDOrg
        SQL;
        $rows = DB::select($SQL);

        if(count($rows) > 0){
            foreach($rows as $r){
                $Options = "<a href='".route('Professores/Contratos/Edit',$r->id)."' class='btn btn-primary btn-xs'>Abrir</a> ";
                if($r->STContrato == 1){
                    $Options .="&nbsp;  <a class='btn btn-danger btn-xs' href='".route('Professores/Contratos/Cancelar',$r->id)."'>Cancelar</a> ";
                }else{
                    $Options .="&nbsp;  <strong class='text-danger'>Cancelado</strong>";
                }

                $item = [];
                $item[] = $r->Professor;
                $item[] = $r->Contrato;
                $item[] = $r->Salario;
                $item[] = $r->Inicio;
                $item[] = $r->Termino;
                $item[] = $Options;
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($rows)),
            "recordsFiltered" => intval(count($rows)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }

    public function cancelarContrato($id){
        Contrato::find($id)->update([
            "STContrato" => 0
        ]);

        return redirect()->back();
    }

    public function cadastroContratos($id=null){
        $view = [
            "submodulos" => self::submodulos,
            "Professores" => self::getProfessoresRede(Auth::user()->id_org),
            "id" => ""
        ];

        if($id){
            $Contrato = Contrato::find($id);
            $view['Registro'] = $Contrato;
            $view['Aditivos'] = json_decode($Contrato->Aditivos);
            $view['id'] = $id;
        }

        return view('Professores.cadastroContratos',$view);
    }

    public function saveContratos(Request $request){
        try{
            $data = $request->all();

            if($request->id){
                Contrato::find($request->id)->update($data);
                $mensagem = "Sala Editada com Sucesso!";
                $rota = 'Professores/Contratos/Edit';
                $aid = $request->id;
            }else{
                Contrato::create($data);
                $mensagem = "Sala cadastrada com Sucesso!";
                $aid = '';
                $rota = 'Professores/Contratos/Novo';
            }
            $status = 'success';
        }catch(\Throwable $th){
            $rota = 'Professores/Contratos/Novo';
            $mensagem = 'Erro '.$th;
            $aid = '';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function saveAditivos($IDContrato,Request $request){
        $Contrato = Contrato::find($IDContrato);
        if(!is_null($Contrato->Aditivos)){
            $Aditivo = json_decode($Contrato->Aditivos,true);

            array_push($Aditivo,array(
                "Nome" => $request->Nome,
                "Data" => $request->Data
            ));

            $Contrato->update([
                "Aditivos" => json_encode($Aditivo)
            ]);
        }else{
            $Contrato->update([
                "Aditivos" => json_encode(array([
                    "Nome" => $request->Nome,
                    "Data" => $request->Data
                ]))
            ]);
        }

        return redirect()->back();
    }

    public static function getEscolaProfessores(){
        if(Auth::user()->tipo == 4){
            $IDEscolas = self::getEscolaDiretor(Auth::user()->id);
        }elseif(Auth::user()->tipo == 5){
            $IDEscolas = implode(",",PedagogosController::getEscolasPedagogo(Auth::user()->IDProfissional));
        }elseif(in_array(Auth::user()->tipo,[2,2.5])){
            $IDEscolas = implode(',',SecretariasController::getEscolasRede(Auth::user()->id_org));
        }elseif(in_array(Auth::user()->tipo,[4.5,5.5])){
            $IDEscolas = AuxiliaresController::getEscolaAdm(Auth::user()->id);
        }

        $Escolas = array();
        foreach(DB::select("SELECT al.IDProfissional FROM escolas e INNER JOIN alocacoes al ON(al.IDEscola = e.id) WHERE al.TPProfissional = 'PROF' AND al.IDEscola IN($IDEscolas) ") as $p){
            array_push($Escolas,$p->IDProfissional);
        }
        
        return $Escolas;
    }

    public static function getIdTurmasProfessor($IDProfissional,$Tipo){
        $return = array();
        $SQL = <<<SQL
        SELECT 
            t.id,
            t.Nome,
            t.Serie,
            e.Nome as Escola
        FROM turnos tn
        INNER JOIN turmas t ON(tn.IDTurma = t.id)
        INNER JOIN alocacoes al ON(t.IDEscola = al.IDEscola)
        INNER JOIN escolas e ON(al.IDEscola = e.id)
        INNER JOIN professores p ON(p.id = tn.IDProfessor)
        INNER JOIN users us ON(us.IDProfissional = p.id)
        INNER JOIN disciplinas d ON(d.id = tn.IDDisciplina)
        WHERE us.id = $IDProfissional GROUP BY t.Nome,t.Serie
        SQL;
        $Turmas = DB::select($SQL);
        if($Tipo == "ARRAY"){
            foreach($Turmas as $t){
                array_push($return,$t);
            }
        }else{
            foreach($Turmas as $t){
                array_push($return,$t->id);
            }
        }

        return $return;
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
        $SQL = "SELECT m.Nome as Aluno,a.id,t.Nome as Turma,t.Serie,e.Nome as Escola FROM alunos a INNER JOIN matriculas m on(a.IDMatricula = m.id) INNER JOIN turmas t ON(t.id = a.IDTurma) INNER JOIN turnos tn ON(tn.IDTurma = t.id) INNER JOIN escolas e ON(e.id = t.IDEscola) WHERE tn.IDProfessor = $IDProfessor AND STAluno = 0";
        return DB::select($SQL);
    }

    public static function getIdAlunosProfessor($IDProfessor){
        $ID = [];
        $SQL = "SELECT a.id FROM alunos a INNER JOIN matriculas m on(a.IDMatricula = m.id) INNER JOIN turmas t ON(t.id = a.IDTurma) INNER JOIN turnos tn ON(tn.IDTurma = t.id) WHERE tn.IDProfessor = $IDProfessor AND STAluno = 0";
        
        foreach(DB::select($SQL) as $s){
            array_push($ID,$s->id);
        }

        return $ID;
    }

    public static function getDisciplinasProfessor($IDProfessor){
        $SQL = "SELECT d.NMDisciplina as Disciplina,d.id as IDDisciplina FROM disciplinas d INNER JOIN turnos t ON(d.id = t.IDDisciplina) WHERE t.IDProfessor = $IDProfessor GROUP BY d.id";
        return DB::select($SQL);
    }

    public function getProfessores(){

        $AND = " AND a.IDEscola IN(".implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)).")";

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
        FROM professores p
        INNER JOIN alocacoes a ON a.IDProfissional = p.id
        INNER JOIN escolas e ON e.id = a.IDEscola
        INNER JOIN organizacoes o ON e.IDOrg = o.id
        WHERE o.id = $orgId $AND AND a.TPProfissional = "PROF"
        GROUP BY p.id, p.Nome, p.Admissao, p.TerminoContrato, p.CEP, p.Rua, p.UF, p.Cidade, p.Bairro, p.Numero;
        SQL;

        $Professores = DB::select($SQL);

        if(count($Professores) > 0){
            foreach($Professores as $d){
                $item = [];
                $item[] = $d->Professor;
                $item[] = Controller::data($d->Admissao,'d/m/Y');
                $item[] = Controller::data($d->TerminoContrato,'d/m/Y');
                (in_array(Auth::user()->tipo,[2,2.5])) ? $item[] = implode(",",json_decode($d->Escolas)) : '';
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
            t.Serie,
            tur.DiaSemana,
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
                (in_array(Auth::user()->tipo,[2,2.5])) ? $item[] = $t->Escola : '';
                $item[] = $t->Serie." - ".$t->Turma;
                $item[] = $t->Disciplina;
                $item[] = $t->DiaSemana;
                $item[] = Controller::data($t->Inicio,'H:i');
                $item[] = Controller::data($t->Termino,'H:i');
                $item[] = "
                <a href='".route('Professores/Turnos/Edit',['idprofessor' => $idprofessor,'id' => $t->IDTurno])."' class='btn btn-primary btn-xs'>Editar</a>
                <a href='".route('Professores/Turnos/Remove',$t->IDTurno)."' class='btn btn-danger btn-xs'>Excluir</a>
                ";
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
            'Escolas' => DB::select("SELECT e.Nome as Escola, e.id as IDEscola FROM escolas e INNER JOIN alocacoes a ON(e.id = a.IDEscola) INNER JOIN professores p ON(p.id = a.IDProfissional) WHERE p.id = $idprofessor GROUP BY e.id "),
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
                t.id as IDTurma,
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
            $view['IDEscola'] = $IDEscola;

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
                CASE 
                    WHEN (SELECT COUNT(IDEscola) 
                        FROM alocacoes a2 
                        WHERE a2.IDProfissional = $id 
                            AND a2.IDEscola = e.id 
                            AND a2.TPProfissional = 'PROF') > 0 THEN 1 
                    ELSE 0 
                END AS Alocado
            FROM escolas e 
            LEFT JOIN alocacoes a ON e.id = a.IDEscola AND a.IDProfissional = $id AND a.TPProfissional = 'PROF'
            INNER JOIN organizacoes o ON e.IDOrg = o.id
            WHERE o.id = $orgId
            GROUP BY e.id, e.Nome, a.INITurno, a.TERTurno
            ORDER BY e.Nome;
            SQL;

           
            $escolasUm = Controller::array_associative_unique(DB::select($sqlScola));
            $SQL = <<<SQL
            SELECT 
                p.*,
                u.STAcesso,
                u.id as IDUser
            FROM professores p
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

    public static function getNomeDisiciplinasProfessor($IDUser){
        $arr = array();
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
        WHERE us.id = $IDUser GROUP BY d.id
        SQL;
        foreach(DB::select($SQL) as $s){
            array_push($arr,array(
                "IDDisciplina" => $s->IDDisciplina,
                "Disciplina" => $s->Disciplina,
            ));
        }
        return $arr;
    }

    public static function getTurmasProfessor($id){
        $SQL = <<<SQL
        SELECT 
            t.id,
            t.Nome,
            t.Serie,
            e.Nome as Escola
        FROM turnos tn
        INNER JOIN turmas t ON(tn.IDTurma = t.id)
        INNER JOIN alocacoes al ON(t.IDEscola = al.IDEscola)
        INNER JOIN escolas e ON(al.IDEscola = e.id)
        INNER JOIN professores p ON(p.id = tn.IDProfessor)
        INNER JOIN users us ON(us.IDProfissional = p.id)
        INNER JOIN disciplinas d ON(d.id = tn.IDDisciplina)
        WHERE us.id = $id GROUP BY t.Nome,t.Serie
        SQL;

        return DB::select($SQL);

    }

    public function getDisciplinasTurmaProfessor($IDTurma,$IDProfessor=null){
        
        if(Auth::user()->tipo == 6){
            $id = Auth::user()->id;   
        }else{
            $id = $IDProfessor;
        }

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
        WHERE p.id = $id AND t.id = $IDTurma GROUP BY d.id
        SQL;
        ob_start();
        foreach(DB::select($SQL) as $tp){
        ?>
        <div>
            <input type="checkbox" name="IDDisciplina[]" value="<?=$tp->IDDisciplina?>"><?=$tp->Disciplina?>
        </div>
        <?php
        }
        return ob_get_clean();
        //return json_encode(DB::select($SQL));
    }

    public function getSelectDisciplinasTurmaProfessor($IDTurma,$IDProfessor=null){
        
        if(Auth::user()->tipo == 6){
            $id = Auth::user()->id;   
        }else{
            $id = $IDProfessor;
        }

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
        WHERE p.id = $id AND t.id = $IDTurma GROUP BY d.id
        SQL;
        ob_start();
        foreach(DB::select($SQL) as $tp){
        ?>
        <option value="<?=$tp->IDDisciplina?>"><?=$tp->Disciplina?></option>
        <?php
        }
        return ob_get_clean();
        //return json_encode(DB::select($SQL));
    }

    public function saveTurno(Request $request){
        try{
            if($request->id){
                //dd(Turno::find($request->id)->toArray());
                //dd($request->all());
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

    public function Imprimir(){
        $AND = " AND a.IDEscola IN(".implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)).")";

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
            p.CPF,
            p.Cidade,
            p.Celular,
            p.Bairro,
            p.Numero,
            p.Email,
            p.Nascimento
        FROM professores p
        INNER JOIN alocacoes a ON a.IDProfissional = p.id
        INNER JOIN escolas e ON e.id = a.IDEscola
        INNER JOIN organizacoes o ON e.IDOrg = o.id
        WHERE o.id = $orgId $AND AND a.TPProfissional = "PROF"
        GROUP BY p.id, p.Nome, p.Admissao, p.TerminoContrato, p.CEP, p.Rua, p.UF, p.Cidade, p.Bairro, p.Numero;
        SQL;

        $Professores = DB::select($SQL);

        $pdf = new FPDF();
        $pdf->AddPage();
        // Definir margens
        $pdf->SetMargins(3, 3, 3); // Margens esquerda, superior e direita
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, self::utfConvert("Lista de Professores"), 0, 1, 'C'); // Nome da escola centralizado
        $pdf->Ln(10);
        //CABECALHO DA TABELA
        $pdf->Ln();
        
        foreach($Professores as $prof){
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 6, self::utfConvert($prof->Professor),0,1,"C");
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(0, 6, self::utfConvert('Endereço: '.self::utfConvert($prof->Rua.", ".$prof->Numero." - ".$prof->Bairro." ".$prof->Cidade." ".$prof->UF)),0, 1);
            $pdf->Cell(0, 6, self::utfConvert('Nascimento: '. date('d/m/Y', strtotime($prof->Nascimento))),0, 1);
            $pdf->Cell(0, 6, self::utfConvert('Sexo: M'),0, 1);
            $pdf->Cell(0, 6, self::utfConvert('Email: '.$prof->Email),0, 1);
            $pdf->Cell(0, 6, self::utfConvert('Celular: '.$prof->Celular),0, 1);
            $pdf->Cell(0, 6, self::utfConvert('CPF: '.$prof->CPF),0, 1);
            $pdf->Cell(0, 6, self::utfConvert('NIS: 1234324'),0, 1);
            $pdf->Ln();
        }
        $pdf->SetFont('Arial', '', 6);
        $pdf->Output('I',"Alunos Recuperacao".'.pdf');
        exit;
    }
    
    public function removeTurno($IDTurno){
        $status = 0;
        Turno::find($IDTurno)->delete();
        $status = 1;
        return redirect()->back();
    }

    public function save(Request $request){
        try{
            $aid = '';
            $dir = $request->all();
            $dir['CEP'] = preg_replace('/\D/', '', $request->CEP);
            $dir['Celular'] = preg_replace('/\D/', '', $request->Celular);
            $dir['CPF'] = preg_replace('/\D/', '', $request->CPF);
            if($request->id){
                $Professor = Professor::find($request->id);
                $Professor->update($dir);
                $rout = 'Professores/Edit';
                $aid = $request->id;
                if($request->credenciais){
                    $rnd = rand(100000,999999);
                    SMTPController::send($request->Email,"FR Educacional",'Mail.senha',array("Senha"=>$rnd,"Email"=>$request->Email));
                    $mensagem = 'Salvamento Feito com Sucesso! as Novas Credenciais de Login foram Enviadas no Email Cadastrado';
                    $Usuario = User::find($request->IDUser);
                    $Usuario->update([
                        'name' => $request->Nome,
                        'email' => $request->Email,
                        'tipo' => 6,
                        'password' => Hash::make($rnd)
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
                $rnd = rand(100000,999999);
                SMTPController::send($request->Email,"FR Educacional",'Mail.senha',array("Senha"=>$rnd,"Email"=>$request->Email));
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

                $usId = User::create([
                    'name' => $request->Nome,
                    'email' => $request->Email,
                    'tipo' => 6,
                    'password' => Hash::make($rnd),
                    'IDProfissional' => $profId->id,
                    'id_org' => Auth::user()->id_org
                ]);

                Professor::find($profId->id)->update(["IDUser"=>$usId->id]);
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
