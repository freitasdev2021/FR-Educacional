<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Escola;
use App\Models\Calendario;
use App\Models\Turma;
use App\Models\Disciplina;
use App\Models\alocacoesDisciplinas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
    ]);

    public const professoresSubmodulos = array([
        "nome" => "Cadastro",
        "endereco" => "index",
        "rota" => "Turmas/index"
    ]);

    public function index(){
        $view = [
            "submodulos" => self::submodulos,
            'id' => ''
        ];
        if(Auth::user()->tipo == 4){
            $view['Registro'] = Escola::where('id',self::getEscolaDiretor(Auth::user()->id))->first();
        }
        return view('Escolas.index',$view);
    }

    public function getEscolas(){
        if(Escola::count() > 0){
            foreach(Escola::all()->where('IDOrg',Auth::user()->id_org) as $e){
                $item = [];
                $item[] = $e->Nome;
                $item[] = $e->Rua." ".$e->Numero." ".$e->Bairro." - ".$e->Cidade."/".$e->UF;
                $item[] = $e->Email;
                $item[] = $e->Telefone;
                $item[] = $e->QTVagas;
                $item[] = "<a href='".route('Escolas/Edit',$e->id)."' class='btn btn-primary btn-xs'>Editar</a>";
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
                $Escola->update($esc);
                $rout = 'Escolas/Edit';
                $aid = $request->id;
            }else{
                Escola::create($esc);
                $rout = 'Escolas/Novo';
            }
            $status = 'success';
            $mensagem = 'Salvamento Feito com Sucesso';
        }catch(\Throwable $th){
            $status = 'error';
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
                    'TERAno' => $request->TERAno
                ]);
                $aid = $request->id;
            }else{
                Calendario::create([
                    'INIAno' => $request->INIAno,
                    'TERAno' => $request->TERAno,
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

        if(Auth::user()->tipo == 4){
            $IDEscola = self::getEscolaDiretor(Auth::user()->id);
            $AND = ' AND ad.IDEscola='.$IDEscola;
            //dd($AND);
        }else{
            $AND = '';
        }

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
                (Auth::user()->tipo == 2) ? $item[] = implode(",",json_decode($d->Escolas)) : '';
                $item[] = "<a href='".route('Escolas/Disciplinas/Cadastro',$d->id)."' class='btn btn-primary btn-xs'>Editar</a>";
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
            'escolas' => DB::select("SELECT e.Nome,e.id FROM escolas e LEFT JOIN alocacoes_disciplinas ad ON(ad.IDEscola = e.id) WHERE e.IDOrg = $idorg")
        ];

        if($id){
            $SQL = <<<SQL
            SELECT 
                e.id as IDEscola,
                d.NMDisciplina, 
                d.Obrigatoria, 
                d.id, 
                (SELECT CONCAT('[', GROUP_CONCAT('"', e2.Nome, '"' SEPARATOR ','), ']')
                FROM escolas e2 
                WHERE e2.id IN 
                    (SELECT e3.id 
                    FROM disciplinas d3 INNER JOIN alocacoes_disciplinas ad3 ON(d.id = ad.IDDisciplina) INNER JOIN escolas e3 ON(e3.id = ad3.IDEscola) 
                    WHERE d3.NMDisciplina = d.NMDisciplina)
                ) as Escolas
            FROM 
                disciplinas d
            INNER JOIN
                alocacoes_disciplinas ad ON(ad.IDDisciplina = d.id)
            INNER JOIN 
                escolas e ON (ad.IDEscola = e.id)
            INNER JOIN 
                organizacoes o ON(e.IDOrg = o.id)
            WHERE d.id = $id
            GROUP BY 
                d.NMDisciplina;
            SQL;


            $disciplinas = DB::select($SQL);
            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = "Escolas/Edit";
            $view['id'] = $id;
            $view['Registro'] = $disciplinas[0];
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

    public function getDisciplinasEscola($IDEscola){
        $SQL = <<<SQL
        SELECT 
            d.NMDisciplina as Disciplina,
            d.id as IDDisciplina
        FROM disciplinas d
        INNER JOIN alocacoes_disciplinas ad ON(ad.IDDisciplina = d.id)
        WHERE ad.IDEscola = $IDEscola
        SQL;

        return json_encode(DB::select($SQL),JSON_UNESCAPED_UNICODE);
    }

    public function saveDisciplinas(Request $request){
        try{
            $aid = ''; 
            if($request->id){

                alocacoesDisciplinas::where('IDDisciplina',$request->id)->delete();
                Disciplina::find($request->id)->update(['NMDisciplina'=>$request->NMDisciplina,'Obrigatoria' =>$request->Obrigatoria]);

                foreach($request->Escola as $df){
                    alocacoesDisciplinas::create([
                        "IDDisciplina" => $request->id,
                        "IDEscola" => $df
                    ]);
                }
                $rout = 'Escolas/Disciplinas/Cadastro';
                $aid = $request->id;
            }else{
                $crieite = Disciplina::create(['NMDisciplina'=>$request->NMDisciplina,'Obrigatoria' =>$request->Obrigatoria]);
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
        $SQL = "SELECT 
            t.id as IDTurma,
            t.Nome as Turma,
            t.Serie,
            e.Nome as Escola, 
            CASE WHEN pa.id = t.IDPlanejamento THEN 1 ELSE 0 END as Alocada 
        FROM turmas t 
        INNER JOIN escolas e ON(t.IDEscola = e.id) 
        LEFT JOIN turnos tur ON(t.id = tur.IDTurma) 
        LEFT JOIN planejamentoanual pa ON(pa.id = t.IDPlanejamento) 
        WHERE tur.IDDisciplina = $IDDisciplina AND t.IDPlanejamento = 0";
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
    ///////////////////////////////////////////TURMAS
    public function getTurmas(){

        $idorg = Auth::user()->id_org;

        if(Auth::user()->tipo == 6){
            $AND = ' AND t.id IN('.implode(',',self::getFichaProfessor(Auth::user()->id,'Turmas')).')';
        }elseif(Auth::user()->tipo == 4){
            $AND = ' AND t.IDEscola = '.self::getEscolaDiretor(Auth::user()->id);
        }else{
            $AND = '';
        }

        $SQL = <<<SQL
        SELECT t.id as IDTurma, t.Nome as Turma,t.INITurma,t.TERTurma,e.Nome as Escola,t.Serie 
        FROM turmas t
        INNER JOIN escolas e ON(e.id = t.IDEscola)
        INNER JOIN organizacoes o on(e.IDOrg = o.id)
        WHERE o.id = $idorg $AND
        SQL;

        $turmas = DB::select($SQL);
        if(count($turmas) > 0){
            foreach($turmas as $t){
                $item = [];
                $item[] = $t->Turma;
                $item[] = $t->Serie;
                (Auth::user()->tipo == 2) ? $item[] = $t->Escola : '';
                (in_array(Auth::user()->tipo,[2,4])) ? $item[] = $t->INITurma." - ".$t->TERTurma : '';
                $item[] = 0;
                (in_array(Auth::user()->tipo,[2,4])) ? $item[] = "<a href='".route('Escolas/Turmas/Cadastro',$t->IDTurma)."' class='btn btn-primary btn-xs'>Editar</a>" : '';
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

    public function cadastroTurmas($id=null){

        $view = [
            "submodulos" => self::submodulos,
            'id' => '',
            'escolas' => Escola::all()->where('IDOrg',Auth::user()->id_org)
        ];

        if($id){
            $SQL = <<<SQL
            SELECT t.id as IDTurma,t.Periodo, t.Nome as Turma,t.INITurma,t.TERTurma,e.id as IDEscola,t.Serie,t.NotaPeriodo,t.MediaPeriodo,t.TotalAno
            FROM turmas t
            INNER JOIN escolas e ON(e.id = t.IDEscola)
            INNER JOIN organizacoes o on(e.IDOrg = o.id)
            WHERE t.id = $id
            SQL;

            $turmas = DB::select($SQL);
            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = "Escolas/Turmas/Cadastro";
            $view['id'] = $id;
            $view['Registro'] = $turmas[0];
        }

        return view('Escolas.createTurmas',$view);

    }

    public function saveTurmas(Request $request){
        try{
            $aid = '';
            $turma = $request->all();
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
            $mensagem = "Erro ao Salvar a Turma: ".$th;
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }
    /////////////////////////////////////////////

}
