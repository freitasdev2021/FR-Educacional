<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Escola;
use App\Models\Calendario;
use App\Models\Turma;
use App\Models\Disciplina;
use App\Models\Sala;
use App\Models\alocacoesDisciplinas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Storage;

class EscolasController extends Controller
{

    public const submodulos = array([
        "nome" => "Cadastro",
        "endereco" => "index",
        "rota" => "Escolas/index"
    ],[
        "nome" => "Disciplinas",
        "endereco" => "Disciplinas",
        "rota" => "Escolas/Disciplinas"
    ],[
        "nome" => "Turmas",
        "endereco" => "Turmas",
        "rota" => "Escolas/Turmas"
    ],[
        "nome" => "Salas",
        "endereco" => "Salas",
        "rota" => "Escolas/Salas"
    ],[
        "nome" => "Vagas",
        "endereco"=> "Vagas",
        "rota" => "Escolas/Vagas"
    ],[
        "nome" => "Relatorios",
        "endereco"=> "Relatorios",
        "rota" => "Escolas/Relatorios"
    ]);

    public const professoresSubmodulos = array([
        "nome" => "Cadastro",
        "endereco" => "index",
        "rota" => "Turmas/index"
    ]);

    public function relatorios(){
        return view('Escolas.relatorios',[
            "submodulos" => self::submodulos
        ]);
    }

    public function index(){
        $view = [
            "submodulos" => self::submodulos,
            'id' => ''
        ];
        //dd(Auth::user()->IDProfissional);
        //dd(self::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional));
        if(in_array(Auth::user()->tipo,[4,4.5])){
            $view['Registro'] = Escola::where('id',self::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional))->first();
        }
        return view('Escolas.index',$view);
    }

    public static function getIdEscolas($Tipo,$ID,$IDOrg = null,$IDProfissional = null) {
        if ($Tipo == 4.0) {
            $return = array(self::getEscolaDiretor($ID));
        } elseif ($Tipo == 4.5) {
            $return = array(AuxiliaresController::getEscolaAdm($ID));
        } elseif ($Tipo == 2.0 || $Tipo == 2.5) {
            $return = SecretariasController::getEscolasRede($IDOrg);
        } elseif ($Tipo == 5.0) {
            $return = PedagogosController::getEscolasPedagogo($IDProfissional);
        } elseif ($Tipo == 5.5) {
            $return = array(AuxiliaresController::getEscolaAdm($ID));
        } elseif ($Tipo == 6.0) {
            $return = ProfessoresController::getEscolasProfessor($IDProfissional);
        } elseif ($Tipo == 6.5) {
            $return = array(AuxiliaresController::getEscolaAdm($ID));
        } else {
            $return = "Este usuário não Pertence a nenhuma Escola"; // Caso o valor de $Tipo não se encaixe em nenhuma condição
        }
    
        return $return;
    }    

    public function getEscolas(){
        if(Escola::where('IDOrg',Auth::user()->id_org)->count() > 0){
            foreach(Escola::all()->where('IDOrg',Auth::user()->id_org) as $e){
                $item = [];
                $item[] = $e->Nome;
                $item[] = $e->Rua." ".$e->Numero." ".$e->Bairro." - ".$e->Cidade."/".$e->UF;
                $item[] = $e->Email;
                $item[] = $e->Telefone;
                $item[] = $e->QTVagas;
                $item[] = "<a href='".route('Escolas/Edit',$e->id)."' class='btn btn-primary btn-xs'>Editar</a> <a href='".route('Relatorios/Escolas/Quadro',$e->id)."' class='btn btn-primary btn-xs'>Quadro de Turmas</a>";
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(Escola::count()),
            "recordsFiltered" => intval(Escola::count()),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }

    public function cadastro($id=null){

        $view = [
            "submodulos" => self::submodulos,
            'id' => ''
        ];

        if($id){
            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = "Escolas/Edit";
            $view['id'] = $id;
            $view['Registro'] = Escola::all()->where('id',$id)->first();
            $view['end'] = json_decode($view['Registro']->Endereco);
        }

        return view('Escolas.createEscola',$view);

    }

    public function save(Request $request){
        try{
            $aid = '';
            $esc = $request->all();
            $esc['CEP'] = preg_replace('/\D/', '', $request->CEP);
            $esc['Telefone'] = preg_replace('/\D/', '', $request->Telefone);
            if($request->id){
                $Escola = Escola::find($request->id);
                //dd($request->file('Foto'));
                if($request->file('Foto')){
                    Storage::disk('public')->delete('organizacao_'.Auth::user()->id_org.'_escolas/escola_'. $request->id . '/' . $request->oldFoto);
                    $Foto = $request->file('Foto')->getClientOriginalName();
                    $request->file('Foto')->storeAs('organizacao_'.Auth::user()->id_org.'_escolas/escola_'.$request->id,$Foto,'public');
                    $esc['Foto'] = $Foto;
                }else{
                    $Foto = '';
                }
                
                $Escola->update($esc);
                $rout = 'Escolas/Edit';
                $aid = $request->id;
            }else{
                if($request->file('Foto')){
                    $Foto = $request->file('Foto')->getClientOriginalName();
                    $esc['Foto'] = $Foto;
                }
                $IDEscola = Escola::create($esc);
                $request->file('Foto')->storeAs('organizacao_'.Auth::user()->id_org.'_escolas/escola_'.$IDEscola->id,$Foto,'public');
                $rout = 'Escolas/Novo';
            }
            $status = 'success';
            $mensagem = 'Salvamento Feito com Sucesso';
        }catch(\Throwable $th){
            $status = 'error';
            $rout = 'Escolas/Novo';
            $mensagem = "Erro ao Salvar a Escola: ".$th;
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    ////////////////////////////////////////////ANOS LETIVOS
    public function getAnosLetivos(){
        $anosletivos = DB::select("SELECT c.INIAno,c.TERAno,c.id,e.Nome FROM calendario c INNER JOIN escolas e ON(c.IDEscola = e.id) INNER JOIN organizacoes o ON(e.IDOrg = o.id) WHERE DATE_FORMAT(INIAno, '%Y') = date('Y') AND DATE_FORMAT(TERAno, '%Y') = date('Y') AND o.id = '".Auth::user()->id_org."'  ");

        if(count($anosletivos) > 0){
            foreach($anosletivos as $c){
                $item = [];
                $item[] = $c->Nome;
                $item[] = Controller::data($c->INIAno,'d/m/Y');
                $item[] = Controller::data($c->TERAno,'d/m/Y');
                $item[] = "<a href='".route('Escolas/Anosletivos/Cadastro',$c->id)."' class='btn btn-primary btn-xs'>Editar</a>";
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($anosletivos)),
            "recordsFiltered" => intval(count($anosletivos)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }

    public function anosLetivos(){
        return view('Escolas.anosLetivos',[
            "submodulos" => self::submodulos,
            'id' => ''
        ]);
    }

    public function cadastroAnosLetivos($id=null){

        $view = [
            "submodulos" => self::submodulos,
            'id' => '',
            'escolas' => Escola::all()->where('IDOrg',Auth::user()->id_org)
        ];

        if($id){
            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = "Escolas/Anosletivos/Cadastro";
            $view['id'] = $id;
            $view['Registro'] = Calendario::all()->where('id',$id)->first();
            $view['end'] = json_decode($view['Registro']->Endereco);
        }

        return view('Escolas.createAnosLetivos',$view);

    }

    public function saveAnosLetivos(Request $request){
        try{
            $rout = 'Calendario/index';
            if($request->id){
                $AnoLetivo = Calendario::find($request->id);
                $AnoLetivo->update([
                    'INIAno' => $request->INIAno,
                    'TERAno' => $request->TERAno,
                    'INIRematricula' => $request->INIRematricula,
                    'TERRematricula' => $request->TERRematricula
                ]);
                $aid = $request->id;
            }else{
                Calendario::create([
                    'INIAno' => $request->INIAno,
                    'TERAno' => $request->TERAno,
                    'INIRematricula' => $request->INIRematricula,
                    'TERRematricula' => $request->TERRematricula,
                    "IDOrg" => Auth::user()->id_org
                ]);
            }
            $status = 'success';
            $mensagem = 'Salvamento Feito com Sucesso';
        }catch(\Throwable $th){
            $status = 'error';
            $mensagem = "Erro ao Ano Letivo".$th;
        }finally{
            return redirect()->route($rout)->with($status,$mensagem);
        }
    }
    ///////////////////////////////////////////DISCIPLINAS
    public function getDisciplinas(){

        $AND = " AND ad.IDEscola IN(".implode(",",self::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)).")";

        $idorg = Auth::user()->id_org;

        $SQL = <<<SQL
        SELECT NMDisciplina, Obrigatoria,d.id, CONCAT('[', 
                GROUP_CONCAT(
                '"', e.Nome, '"' 
            SEPARATOR ','), 
        ']') as Escolas 
        FROM disciplinas d
        INNER JOIN alocacoes_disciplinas ad ON(d.id = ad.IDDisciplina) 
        INNER JOIN escolas e ON(ad.IDEscola = e.id)
        INNER JOIN organizacoes o ON(e.IDOrg = o.id) WHERE o.id = $idorg $AND
        GROUP BY NMDisciplina ;
        SQL;
        $disciplinas = DB::select($SQL);
        if(count($disciplinas) > 0){
            foreach($disciplinas as $d){
                $item = [];
                $item[] = $d->NMDisciplina;
                (in_array(Auth::user()->tipo,[2,2.5])) ? $item[] = implode(",",json_decode($d->Escolas)) : '';
                $item[] = (in_array(Auth::user()->tipo,[2,2.5])) ? "<a href='".route('Escolas/Disciplinas/Cadastro',$d->id)."' class='btn btn-primary btn-xs'>Editar</a>" : '';
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($disciplinas)),
            "recordsFiltered" => intval(count($disciplinas)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }

    public function disciplinas(){

        return view('Escolas.disciplinas',[
            "submodulos" => self::submodulos,
            'id' => ''
        ]);
    }

    public function cadastroDisciplinas($id=null){
        $idorg = Auth::user()->id_org;
        $view = [
            "submodulos" => self::submodulos,
            'id' => '',
            'escolas' => DB::select("SELECT e.Nome,e.id FROM escolas e LEFT JOIN alocacoes_disciplinas ad ON(ad.IDEscola = e.id) WHERE e.IDOrg = $idorg GROUP BY e.id")
        ];

        if($id){
            $SQL = <<<SQL
            SELECT 
                e.Nome,
                e.id,
                MAX(CASE WHEN d.id IS NOT NULL THEN 1 ELSE 0 END) AS Alocado
            FROM 
                escolas e 
            LEFT JOIN 
                alocacoes_disciplinas ad ON e.id = ad.IDEscola AND ad.IDDisciplina = $id
            LEFT JOIN 
                disciplinas d ON d.id = ad.IDDisciplina
            WHERE 
                e.IDOrg = $idorg
            GROUP BY 
                e.Nome, e.id;
            SQL;


            $Escolas = DB::select($SQL);
            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = "Escolas/Edit";
            $view['id'] = $id;
            $view['escolas'] = $Escolas;
            $view['Turmas']= self::getTurmasDisciplinas($id,"ARRAY");
            $view['Registro'] = Disciplina::find($id);
        }

        return view('Escolas.createDisciplinas',$view);

    }

    public static function getDisciplinasProfessor($id){
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
        WHERE us.id = $id GROUP BY d.id
        SQL;

        return DB::select($SQL);
    }

    public static function getListDisciplinasEscola($ARREscolas){
        $IDEscolas = implode(',',$ARREscolas);
        $SQL = <<<SQL
        SELECT
            d.id as IDDisciplina,
            d.NMDisciplina as Disciplina
        FROM disciplinas d
        INNER JOIN alocacoes_disciplinas al ON(al.IDDisciplina = d.id)
        WHERE al.IDEscola IN($IDEscolas)
        SQL;

        return DB::select($SQL);
    }

    public function getProfessoresTurmaHTML($IDTurma){
        $AND = " AND tn.IDTurma = ".$IDTurma;

        $SQL = "SELECT 
            p.Nome as Professor,us.id as USProfessor,p.id as IDProfessor 
        FROM professores p 
        INNER JOIN users us ON(us.IDProfissional = p.id)
        INNER JOIN turnos tn ON(p.id = tn.IDProfessor)
        WHERE us.Tipo = 6 $AND GROUP BY p.id";

        $Query = DB::select($SQL);
        ob_start();
        echo "<option value=''>Selecione</option>";
        foreach($Query as $q){
        ?>
        <option value="<?=$q->IDProfessor?>"><?=$q->Professor?></option>
        <?php
        }

        return ob_get_clean();
    }

    public static function getProfessorDisciplina($IDDisciplina,$IDTurma){
        $SQL = <<<SQL
        SELECT
            p.Nome as Professor
        FROM turnos tn
        INNER JOIN turmas t ON(tn.IDTurma = t.id)
        INNER JOIN alocacoes al ON(t.IDEscola = al.IDEscola)
        INNER JOIN escolas e ON(al.IDEscola = e.id)
        INNER JOIN professores p ON(p.id = tn.IDProfessor)
        INNER JOIN users us ON(us.IDProfissional = p.id)
        INNER JOIN disciplinas d ON(d.id = tn.IDDisciplina)
        WHERE d.id = $IDDisciplina AND t.id = $IDTurma GROUP BY p.id
        SQL;

        return DB::select($SQL)[0];
    }

    public static function getNomeDisciplinasEscola(){
        $IDEscolas = implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional));

        $SQL = <<<SQL
        SELECT 
            d.id as IDDisciplina,
            d.NMDisciplina as Disciplina
        FROM disciplinas d
        INNER JOIN alocacoes_disciplinas ad ON(ad.IDDisciplina = d.id)
        WHERE ad.IDEscola IN($IDEscolas)
        SQL;

        return DB::select($SQL);
    }

    public static function getDisciplinasEscola(){
        $IDDisciplinas = array();
        $IDEscolas = implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional));

        $SQL = <<<SQL
        SELECT 
            d.id as IDDisciplina
        FROM disciplinas d
        INNER JOIN alocacoes_disciplinas ad ON(ad.IDDisciplina = d.id)
        WHERE ad.IDEscola IN($IDEscolas)
        SQL;

        foreach(DB::select($SQL) as $d){
            array_push($IDDisciplinas,$d->IDDisciplina);
        }

        return $IDDisciplinas;
    }

    public function saveDisciplinas(Request $request){
        try{
            $aid = ''; 
            if($request->id){

                alocacoesDisciplinas::where('IDDisciplina',$request->id)->delete();
                Disciplina::find($request->id)->update(['NMDisciplina'=>$request->NMDisciplina,'Obrigatoria' =>$request->Obrigatoria,'CargaHoraria'=>$request->CargaHoraria]);

                foreach($request->Escola as $df){
                    alocacoesDisciplinas::create([
                        "IDDisciplina" => $request->id,
                        "IDEscola" => $df
                    ]);
                }
                $rout = 'Escolas/Disciplinas/Cadastro';
                $aid = $request->id;
            }else{
                $crieite = Disciplina::create(['NMDisciplina'=>$request->NMDisciplina,'Obrigatoria' =>$request->Obrigatoria,'CargaHoraria'=>$request->CargaHoraria]);
                foreach($request->Escola as $e){
                    alocacoesDisciplinas::create([
                        "IDDisciplina" => $crieite->id,
                        "IDEscola" => $e
                    ]);
                }
                $rout = 'Escolas/Disciplinas/Novo';
            }
            $status = 'success';
            $mensagem = 'Salvamento Feito com Sucesso';
        }catch(\Throwable $th){
            $status = 'error';
            $mensagem = "Erro ao Salvar a Escola: ".$th;
            $rout = 'Escolas/Disciplinas/Novo';
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }
    /////////////
    public function getTurmasBySerie($Serie){
        $SQL = "SELECT t.id as IDTurma,t.Nome as Turma,t.Serie as Serie WHERE t.Serie = $Serie";
        return DB::select($SQL);
    }



    public function getTurmasDisciplinas($IDDisciplina,$TPRetorno,$IDPlanejamento = null){
        $arrTurmasT = [];
        if(Auth::user()->tipo == 6){
            foreach(ProfessoresController::getTurmasProfessor(Auth::user()->id) as $art){
                array_push($arrTurmasT,$art->IDTurma);
            }
        }else{
            $arrTurmasT = self::getCurrentTurmasEscola();
        }

        $TurmasP = implode(',',$arrTurmasT);
        $SQL = "SELECT 
            t.id as IDTurma,
            prof.id as IDProfessor,
            t.Nome as Turma,
            t.Serie,
            e.Nome as Escola,
            prof.Nome as Professor, 
            CASE WHEN pa.id = t.IDPlanejamento THEN 1 ELSE 0 END as Alocada 
        FROM turmas t 
        INNER JOIN escolas e ON(t.IDEscola = e.id) 
        LEFT JOIN turnos tur ON(t.id = tur.IDTurma)
        LEFT JOIN professores prof ON(prof.id = tur.IDProfessor)
        LEFT JOIN planejamentoanual pa ON(pa.id = t.IDPlanejamento) 
        WHERE tur.IDDisciplina = $IDDisciplina and t.id IN($TurmasP) GROUP BY t.Nome,t.Serie";
        if($TPRetorno == "ARRAY"){
            return DB::select($SQL);
            //echo $SQL;
        }else{
            ob_start();
            foreach(DB::select($SQL) as $d){
                if($d->Alocada && $IDPlanejamento){
                    $alocada = 1;
                }else{
                    $alocada = 0;
                }
        ?>
        <tr>
            <td><input type="checkbox" value="<?=$d->IDTurma?>" <?=($alocada) ? 'checked' : ''?> name="Turma[]"></td>
            <td><?=$d->Turma?></td>
            <td><?=$d->Serie?></td>
            <td><?=$d->Escola?></td>
        </tr>
        <?php
            }
            return ob_get_clean();
        }
    }

    public static function getTurmasSelect(){
        $idorg = Auth::user()->id_org;

        if(Auth::user()->tipo == 6){
            $AND = ' AND t.id IN('.implode(',',ProfessoresController::getIdTurmasProfessor(Auth::user()->id,'ARRAY')).')';
            //dd(ProfessoresController::getIdTurmasProfessor(Auth::user()->IDProfissional,'ARRAY'));
        }else{
            $AND = " AND t.IDEscola IN(".implode(",",self::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)).")";
        }

        $SQL = <<<SQL
        SELECT 
            t.id as IDTurma, 
            t.Nome as Turma,
            e.Nome as Escola,
            t.Serie
        FROM turmas t
        INNER JOIN escolas e ON(e.id = t.IDEscola)
        INNER JOIN organizacoes o on(e.IDOrg = o.id)
        LEFT JOIN alunos a ON(a.IDTurma = t.id)
        WHERE o.id = $idorg $AND
        GROUP BY 
            t.id
        SQL;

        $turmas = DB::select($SQL);
        return $turmas;
    }

    ///////////////////////////////////////////TURMAS
    public function getTurmas(){

        $idorg = Auth::user()->id_org;

        if(Auth::user()->tipo == 6){
            $AND = ' AND t.id IN('.implode(',',ProfessoresController::getIdTurmasProfessor(Auth::user()->id,'tes')).')';
        }else{
            $AND = " AND t.IDEscola IN(".implode(",",self::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)).")";
        }

        $SQL = <<<SQL
        SELECT 
            t.id as IDTurma, 
            COUNT(a.id) as QTAlunos, 
            t.Nome as Turma,t.INITurma,
            t.TERTurma,
            e.Nome as Escola,
            t.Serie,
            t.Periodo,
            (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE au2.IDTurma = t.id ) as Frequencia 
        FROM turmas t
        INNER JOIN escolas e ON(e.id = t.IDEscola)
        INNER JOIN organizacoes o on(e.IDOrg = o.id)
        LEFT JOIN alunos a ON(a.IDTurma = t.id)
        WHERE o.id = $idorg AND a.STAluno = 0 $AND
        GROUP BY 
            t.id, t.Nome, t.INITurma, t.TERTurma, e.Nome, t.Serie
        SQL;

        $turmas = DB::select($SQL);
        if(count($turmas) > 0){
            foreach($turmas as $t){

                switch($t->Periodo){
                    case 'Bimestral':
                        $Estagios = 200/4;
                    break;
                    case 'Trimestral':
                        $Estagios = 200/3;
                    break;
                    case 'Semestral':
                        $Estagios = 200/2;
                    break;
                    case 'Anual':
                        $Estagios = 200/1;
                    break;
                }

                $item = [];
                $item[] = $t->Turma;
                $item[] = $t->Serie;
                (in_array(Auth::user()->tipo,[2,2.5])) ? $item[] = $t->Escola : '';
                (in_array(Auth::user()->tipo,[2,4])) ? $item[] = $t->INITurma." - ".$t->TERTurma : '';
                $item[] = $t->QTAlunos;
                $item[] = "<a href='".route('Escolas/Turmas/Cadastro',$t->IDTurma)."' class='btn btn-primary btn-xs'>Abrir</a> <a href='".route('Turmas/Desempenho',$t->IDTurma)."' class='btn btn-primary btn-xs'>Desempenho</a>";
               
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($turmas)),
            "recordsFiltered" => intval(count($turmas)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }

    public static function turmas(){
        if(Auth::user()->tipo == 6){
            $sub = self::professoresSubmodulos;
        }else{
            $sub = self::submodulos;
        }
        //dd(self::getFichaProfessor(Auth::user()->id,'Horarios'));
        return view('Escolas.turmas',[
            "submodulos" => $sub,
            'id' => ''
        ]);
    }

    public static function getAlunosEscola($escolas){
        $idorg = Auth::user()->id_org;
        $IDAlunos = array();
        if(is_array($escolas)){
            $EscolasImploded = implode(",",$escolas);
        }else{
            $EscolasImploded = "";
        }

        $SQL = "SELECT
            a.id as IDAluno
        FROM matriculas m
        INNER JOIN alunos a ON(a.IDMatricula = m.id)
        LEFT JOIN transferencias tr ON(tr.IDAluno = a.id)
        INNER JOIN turmas t ON(a.IDTurma = t.id)
        INNER JOIN renovacoes r ON(r.IDAluno = a.id)
        INNER JOIN escolas e ON(t.IDEscola = e.id)
        INNER JOIN organizacoes o ON(e.IDOrg = o.id)
        INNER JOIN calendario cal ON(cal.IDOrg = e.IDOrg)
        WHERE e.id IN($EscolasImploded) AND STAluno = 0 GROUP BY a.id";
        
        foreach(DB::select($SQL) as $ds){
            array_push($IDAlunos,$ds->IDAluno);
        }

        return $IDAlunos;
    }

    public static function getNomeAlunosEscola($escolas){
        if(is_array($escolas)){
            $EscolasImploded = implode(",",$escolas);
        }else{
            $EscolasImploded = "";
        }

        $SQL = "SELECT
            a.id,
            m.Nome as Aluno,
            t.Nome as Turma
        FROM matriculas m
        INNER JOIN alunos a ON(a.IDMatricula = m.id)
        LEFT JOIN transferencias tr ON(tr.IDAluno = a.id)
        INNER JOIN turmas t ON(a.IDTurma = t.id)
        INNER JOIN renovacoes r ON(r.IDAluno = a.id)
        INNER JOIN escolas e ON(t.IDEscola = e.id)
        INNER JOIN organizacoes o ON(e.IDOrg = o.id)
        INNER JOIN calendario cal ON(cal.IDOrg = e.IDOrg)
        WHERE e.id IN($EscolasImploded) AND STAluno = 0 GROUP BY a.id";

        return DB::select($SQL);
    }

    public static function getTurmasEscola(){
        $id = Auth::user()->id;
        $IDTurmas = [];
        $turmas = DB::select("SELECT t.id FROM turmas t INNER JOIN auxiliares a ON(a.IDEscola = t.IDEscola) WHERE a.IDUser = $id");
        foreach($turmas as $t){
            array_push($IDTurmas,$t->id);
        }

        return $IDTurmas;
    }

    public static function getSelectTurmasEscola($IDEscolas){
        return Turma::whereIn("IDEscola",$IDEscolas)->get();
    }

    public function getCurrentTurmasEscola(){
        $id = Auth::user()->id;
        $idorg = Auth::user()->id_org;
        $IDTurmas = [];
        $turmas = DB::select("SELECT t.id FROM turmas t INNER JOIN escolas e ON(t.IDEscola = e.id) INNER JOIN organizacoes o ON(e.IDOrg = o.id) WHERE o.id = $idorg");
        foreach($turmas as $t){
            array_push($IDTurmas,$t->id);
        }

        return $IDTurmas;
    }

    public function cadastroTurmas($id=null){
        $idorg = Auth::user()->id_org;
        $Salas = self::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional);
        
        $view = [
            "submodulos" => self::submodulos,
            'id' => '',
            'escolas' => Escola::all()->where('IDOrg',$idorg),
            "Salas" => Sala::all()->whereIn('IDEscola',$Salas)
        ];
        //dd($view['Salas']);
        if($id){
            $SQL = <<<SQL
            SELECT t.id as IDTurma,t.TPAvaliacao,t.MINFrequencia,t.Periodo, t.Nome as Turma,t.INITurma,t.TERTurma,e.id as IDEscola,t.Serie,t.NotaPeriodo,t.MediaPeriodo,t.TotalAno,t.Capacidade,t.Turno
            FROM turmas t
            INNER JOIN escolas e ON(e.id = t.IDEscola)
            INNER JOIN organizacoes o on(e.IDOrg = o.id)
            WHERE t.id = $id
            SQL;
            //
            $Alunos = "SELECT
                a.id as IDAluno, 
                m.Nome as Nome,
                t.Nome as Turma,
                e.Nome as Escola,
                t.Serie as Serie,
                m.Nascimento as Nascimento,
                a.STAluno,
                m.Foto,
                m.Email,
                m.CPF,
                resp.NMResponsavel,
                r.ANO,
                resp.CLResponsavel,
                MAX(tr.Aprovado) as Aprovado,
                cal.INIRematricula,
                cal.TERRematricula,
                cal.INIAno,
                cal.TERAno
            FROM matriculas m
            INNER JOIN alunos a ON(a.IDMatricula = m.id)
            LEFT JOIN transferencias tr ON(tr.IDAluno = a.id)
            INNER JOIN turmas t ON(a.IDTurma = t.id)
            INNER JOIN renovacoes r ON(r.IDAluno = a.id)
            INNER JOIN escolas e ON(t.IDEscola = e.id)
            INNER JOIN organizacoes o ON(e.IDOrg = o.id)
            INNER JOIN calendario cal ON(cal.IDOrg = e.IDOrg)
            INNER JOIN responsavel resp ON(a.id = resp.IDAluno)
            WHERE o.id = $idorg AND t.id = $id AND STAluno = 0 GROUP BY a.id ORDER BY m.Nome ASC 
            ";

            $turmas = DB::select($SQL);
            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = "Escolas/Turmas/Cadastro";
            $view['id'] = $id;
            $view['Alunos'] = DB::select($Alunos);
            $view['Registro'] = $turmas[0];
        }

        return view('Escolas.createTurmas',$view);

    }

    public function saveTurmas(Request $request){
        try{
            $aid = '';
            $turma = $request->all();
            if(in_array(Auth::user()->tipo,[2.5,2])){
                $turma['IDEscola'] = $request->IDEscola;
            }else{
                $turma['IDEscola'] = self::getEscolaDiretor(Auth::user()->id);
            }
            
            if($request->id){
                $Turma = Turma::find($request->id);
                $Turma->update($turma);
                $rout = 'Escolas/Turmas/Cadastro';
                $aid = $request->id;
            }else{
                Turma::create($turma);
                $rout = 'Escolas/Turmas/Novo';
            }
            $status = 'success';
            $mensagem = 'Salvamento Feito com Sucesso';
        }catch(\Throwable $th){
            $status = 'error';
            $rout = 'Escolas/Turmas/Novo';
            $mensagem = "Erro ao Salvar a Turma: ".$th;
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }
    /////////////////////////////////////////////

}
