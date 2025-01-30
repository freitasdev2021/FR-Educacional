<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Turma;
use App\Models\Metas;
use App\Models\AEEPlanejamento;
use App\Models\Escola;
use App\Models\PlanejamentoAnual;
use App\Http\Controllers\ProfessoresController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PlanejamentosController extends Controller
{
    public const submodulos = array([
        "nome" => 'Planejamentos',
        'endereco' => 'index',
        'rota' => 'Planejamentos/index'
    ],[
        "nome" => "Metas",
        "endereco" => "Metas",
        "rota" => "Planejamentos/Metas"
    ],[
        "nome" => "AEE",
        "endereco" => "AEE",
        "rota" => "Planejamentos/AEE"
    ]);

    public const cadastroSubmodulos = array([
        "nome" => 'Cadastro',
        'endereco' => 'Cadastro',
        'rota' => 'Planejamentos/Cadastro'
    ],[
        "nome" => 'Planejamento',
        'endereco' => 'Componentes',
        'rota' => 'Planejamentos/Componentes'
    ]);

    public const aeeSubmodulos = array([
        "nome" => 'Cadastro',
        'endereco' => 'Planejamentos/Cadastro',
        'rota' => 'Planejamentos/AEE/Cadastro'
    ],[
        "nome" => 'Planejamento',
        'endereco' => 'AEE/Componentes',
        'rota' => 'Planejamentos/AEE/Componentes'
    ]);

    public function index(){
        return view('Planejamentos.index',[
            'submodulos' => self::submodulos
        ]);
    }

    public function aee(){
        return view('Planejamentos.aee',[
            'submodulos' => self::submodulos
        ]);
    }

    public function componentes($id){
        $rgs = DB::select("SELECT pa.PLConteudos,t.Periodo FROM planejamentoanual pa INNER JOIN turmas t ON(t.id = pa.IDTurma) WHERE pa.id = $id")[0];
        return view('Planejamentos.componentes',[
            'submodulos' => self::cadastroSubmodulos,
            'id' => $id,
            'Registro' => $rgs,
            'Curriculo' => json_decode(json_decode($rgs->PLConteudos))
        ]);
    }

    public function componentesAee($id){
        $rgs = DB::select("SELECT pa.PLConteudos,t.Periodo FROM planejamento_aee pa INNER JOIN turmas t ON(t.id = pa.IDTurma) WHERE pa.id = $id")[0];
        //dd($rgs);
        return view('Planejamentos.componentesAee',[
            'submodulos' => self::aeeSubmodulos,
            'id' => $id,
            'Registro' => $rgs,
            'Curriculo' => json_decode($rgs->PLConteudos)
        ]);
    }

    public function metas(){
        $IDEscolas = EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional);
        $view = [
            "submodulos" => self::submodulos,
            "Escolas" => Escola::findMany($IDEscolas),
            'IDEscola' => ''
        ];

        if(isset($_GET['IDEscola'])){
            $view['Registro'] = Metas::where('IDEscola',$_GET['IDEscola'])->first();
            $view['IDEscola'] = $_GET['IDEscola'];
        }

        return view("Planejamentos.metas",$view);
    }

    public function saveMeta($IDEscola,Request $request){
        if(Metas::where('IDEscola',$IDEscola)->exists()){
            Metas::where('IDEscola',$IDEscola)->update([
                "MSituacional" => $request->MSituacional,
                "MOperacional" => $request->MOperacional,
                "MConceitual" => $request->MConceitual,
                "IDEscola" => $IDEscola
            ]);
        }else{
            Metas::create([
                "MSituacional" => $request->MSituacional,
                "MOperacional" => $request->MOperacional,
                "MConceitual" => $request->MConceitual,
                "IDEscola" => $IDEscola
            ]);
        }
        return redirect()->back();
    }

    public function saveObjetivo($IDEscola,Request $request){;
        $Metas = Metas::where("IDEscola",$IDEscola)->first();
        //dd($Metas);
        if(!is_null($Metas->MetasJSON)){
            $Meta = json_decode($Metas->MetasJSON,true);
            array_push($Meta,array(
                "Meta" => $request->Meta,
                "Meio" => $request->Meio,
                "Data" => $request->Data,
                )
            );

            $Metas->update([
                "MetasJSON" => json_encode($Meta)
            ]);
        }else{
            $Metas->update([
                "MetasJSON" => json_encode(array([
                    "Meta" => $request->Meta,
                    "Meio" => $request->Meio,
                    "Data" => $request->Data
                ]))
            ]);
        }

        return redirect()->back();
    }

    public function saveComponentes(Request $request){
        try{
            $plan =  PlanejamentoAnual::find($request->IDPlanejamento);
            $plan->update([
                'PLConteudos' => $request->PLConteudos
            ]);
            $retorno['mensagem'] = 'Planejamento Atualizado com Sucesso!';
            $retorno['status'] = $request->IDPlanejamento;
        }catch(\Throwable $th){
            $retorno['status'] = 0;
            $retorno['mensagem'] = $th->getMessage();
        }finally{
            return json_encode($retorno);
        }
    }

    public function saveComponentesAee(Request $request){
        try{
            $plan =  AEEPlanejamento::find($request->IDPlanejamento);
            $plan->update([
                'PLConteudos' => $request->PLConteudos
            ]);
            $retorno['mensagem'] = 'Planejamento Atualizado com Sucesso!';
            $retorno['status'] = $request->IDPlanejamento;
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
            t.id as IDTurma,
            t.Nome as Turma,
            t.Serie,
            e.Nome as Escola,
            CASE WHEN p.IDTurma IS NOT NULL THEN 'checked' ELSE '' END as Checked
        FROM turmas t 
        INNER JOIN escolas e ON(e.id = t.IDEscola)
        LEFT JOIN planejamentoanual p ON(p.IDTurma = t.id)
        GROUP BY t.id
        SQL;

        return DB::select($SQL);
    }

    public function getPlanejamentoByTurma($IDDisciplina,$IDTurma,$TPAula){
        $SQL = DB::select("SELECT pa.PLConteudos,t.Periodo FROM planejamentoanual pa INNER JOIN turmas t ON(t.id = pa.IDTurma) WHERE IDDisciplina = $IDDisciplina AND IDTurma = $IDTurma ")[0];
        $Planejamento = json_decode(json_decode($SQL->PLConteudos,true),true);
        $DTHoje = date('Y-m-d');
        if($TPAula == "Recuperacao"){
            $rec = 1;
        }else{
            $rec = 0;
        }
        switch($SQL->Periodo){
            case 'Bimestral':
                //PRIMEIRO BIMESTRE
                $arrIniPrimeiroB = [];
                $arrTerPrimeiroB = [];
                foreach($Planejamento['primeiroBimestre'] as $pb){
                    array_push($arrIniPrimeiroB,$pb['Inicio']);
                    array_push($arrTerPrimeiroB,$pb['Termino']);
                }
                $ARRDatasPrimeiroB = array_merge($arrIniPrimeiroB,$arrTerPrimeiroB);
                if(count($ARRDatasPrimeiroB) > 0){
                    $INIPrimeiroB = Carbon::parse(self::alternativeUsData($ARRDatasPrimeiroB[0]));
                    $TERPrimeiroB = Carbon::parse(self::alternativeUsData($ARRDatasPrimeiroB[count($ARRDatasPrimeiroB)-1]));
                    if($INIPrimeiroB <= $DTHoje && $TERPrimeiroB >= $DTHoje && $rec == 1){
                        $return['Conteudo'] = $Planejamento['primeiroBimestre'];
                        $return['Estagio'] = "1º BIM";
                    }
                }
                //SEGUNDO BIMESTRE
                $arrIniSegundoB = [];
                $arrTerSegundoB = [];
                foreach($Planejamento['segundoBimestre'] as $sb){
                    array_push($arrIniSegundoB,$sb['Inicio']);
                    array_push($arrTerSegundoB,$sb['Termino']);
                }
                $ARRDatasSegundoB = array_merge($arrIniSegundoB,$arrTerSegundoB);
                if(count($ARRDatasSegundoB)){
                    $INISegundoB = Carbon::parse(self::alternativeUsData($ARRDatasSegundoB[0]));
                    $TERSegundoB = Carbon::parse(self::alternativeUsData($ARRDatasSegundoB[count($ARRDatasSegundoB)-1]));
                    if($INISegundoB <= $DTHoje && $TERSegundoB >= $DTHoje){
                        $return['Conteudo'] = $Planejamento['segundoBimestre'];
                        $return['Estagio'] = "2º BIM";
                    }
                }
                //TERCEIRO BIMESTRE
                $arrIniTerceiroB = [];
                $arrTerTerceiroB = [];
                foreach($Planejamento['terceiroBimestre'] as $tb){
                    array_push($arrIniTerceiroB,$tb['Inicio']);
                    array_push($arrTerTerceiroB,$tb['Termino']);
                }
                $ARRDatasTerceiroB = array_merge($arrIniTerceiroB,$arrTerTerceiroB);
                if(count($ARRDatasTerceiroB) > 0){
                    $INITerceiroB = Carbon::parse(self::alternativeUsData($ARRDatasTerceiroB[0]));
                    $TERTerceiroB = Carbon::parse(self::alternativeUsData($ARRDatasTerceiroB[count($ARRDatasTerceiroB)-1]));
                    if($INITerceiroB <= $DTHoje && $TERTerceiroB >= $DTHoje){
                        $return['Conteudo'] = $Planejamento['terceiroBimestre'];
                        $return['Estagio'] = "3º BIM";
                    }
                }
                //QUARTO BIMESTRE
                $arrIniQuartoB = [];
                $arrTerQuartoB = [];
                foreach($Planejamento['quartoBimestre'] as $qb){
                    array_push($arrIniQuartoB,$qb['Inicio']);
                    array_push($arrTerQuartoB,$qb['Termino']);
                }
                $ARRDatasQuartoB = array_merge($arrIniQuartoB,$arrTerQuartoB);
                if(count($ARRDatasQuartoB) > 0){
                    $INIQuartoB = Carbon::parse(self::alternativeUsData($ARRDatasQuartoB[0]));
                    $TERQuartoB = Carbon::parse(self::alternativeUsData($ARRDatasQuartoB[count($ARRDatasQuartoB)-1]));
                    if($INIQuartoB <= $DTHoje && $TERQuartoB >= $DTHoje){
                        $return['Conteudo'] = $Planejamento['quartoBimestre'];
                        $return['Estagio'] = "4º BIM";
                    }
                }
                /////////////////
            break;
            case 'Trimestral':
                $arrIniPrimeiroB = [];
                $arrTerPrimeiroB = [];
                foreach($Planejamento['primeiroTrimestre'] as $pb){
                    array_push($arrIniPrimeiroB,$pb['Inicio']);
                    array_push($arrTerPrimeiroB,$pb['Termino']);
                }
                $ARRDatasPrimeiroB = array_merge($arrIniPrimeiroB,$arrTerPrimeiroB);
                if(count($ARRDatasPrimeiroB) > 0){
                    $INIPrimeiroB = Carbon::parse(self::alternativeUsData($ARRDatasPrimeiroB[0]));
                    $TERPrimeiroB = Carbon::parse(self::alternativeUsData($ARRDatasPrimeiroB[count($ARRDatasPrimeiroB)-1]));
                    if($INIPrimeiroB <= $DTHoje && $TERPrimeiroB >= $DTHoje){
                        $return['Conteudo'] = $Planejamento['primeiroTrimestre'];
                        $return['Estagio'] = "1º TRI";
                    }
                }
                //SEGUNDO BIMESTRE
                $arrIniSegundoB = [];
                $arrTerSegundoB = [];
                foreach($Planejamento['segundoTrimestre'] as $sb){
                    array_push($arrIniSegundoB,$sb['Inicio']);
                    array_push($arrTerSegundoB,$sb['Termino']);
                }
                $ARRDatasSegundoB = array_merge($arrIniSegundoB,$arrTerSegundoB);
                if(count($ARRDatasSegundoB)){
                    $INISegundoB = Carbon::parse(self::alternativeUsData($ARRDatasSegundoB[0]));
                    $TERSegundoB = Carbon::parse(self::alternativeUsData($ARRDatasSegundoB[count($ARRDatasSegundoB)-1]));
                    if($INISegundoB <= $DTHoje && $TERSegundoB >= $DTHoje){
                        $return['Conteudo'] = $Planejamento['segundoTrimestre'];
                        $return['Estagio'] = "2º TRI";
                    }
                }
                //TERCEIRO BIMESTRE
                $arrIniTerceiroB = [];
                $arrTerTerceiroB = [];
                foreach($Planejamento['terceiroTrimestre'] as $tb){
                    array_push($arrIniTerceiroB,$tb['Inicio']);
                    array_push($arrTerTerceiroB,$tb['Termino']);
                }
                $ARRDatasTerceiroB = array_merge($arrIniTerceiroB,$arrTerTerceiroB);
                if(count($ARRDatasTerceiroB) > 0){
                    $INITerceiroB = Carbon::parse(self::alternativeUsData($ARRDatasTerceiroB[0]));
                    $TERTerceiroB = Carbon::parse(self::alternativeUsData($ARRDatasTerceiroB[count($ARRDatasTerceiroB)-1]));
                    if($INITerceiroB <= $DTHoje && $TERTerceiroB >= $DTHoje){
                        $return['Conteudo'] = $Planejamento['terceiroTrimestre'];
                        $return['Estagio'] = "3º TRI";
                    }
                }
            break;
            case 'Semestral':
                $arrIniPrimeiroB = [];
                $arrTerPrimeiroB = [];
                foreach($Planejamento['primeiroSemestre'] as $pb){
                    array_push($arrIniPrimeiroB,$pb['Inicio']);
                    array_push($arrTerPrimeiroB,$pb['Termino']);
                }
                $ARRDatasPrimeiroB = array_merge($arrIniPrimeiroB,$arrTerPrimeiroB);
                if(count($ARRDatasPrimeiroB) > 0){
                    $INIPrimeiroB = Carbon::parse(self::alternativeUsData($ARRDatasPrimeiroB[0]));
                    $TERPrimeiroB = Carbon::parse(self::alternativeUsData($ARRDatasPrimeiroB[count($ARRDatasPrimeiroB)-1]));
                    if($INIPrimeiroB <= $DTHoje && $TERPrimeiroB >= $DTHoje){
                        $return['Conteudo'] = $Planejamento['primeiroSemestre'];
                        $return['Estagio'] = "1º SEM";
                    }
                }
                //SEGUNDO BIMESTRE
                $arrIniSegundoB = [];
                $arrTerSegundoB = [];
                foreach($Planejamento['segundoSemestre'] as $sb){
                    array_push($arrIniSegundoB,$sb['Inicio']);
                    array_push($arrTerSegundoB,$sb['Termino']);
                }
                $ARRDatasSegundoB = array_merge($arrIniSegundoB,$arrTerSegundoB);
                if(count($ARRDatasSegundoB)){
                    $INISegundoB = Carbon::parse(self::alternativeUsData($ARRDatasSegundoB[0]));
                    $TERSegundoB = Carbon::parse(self::alternativeUsData($ARRDatasSegundoB[count($ARRDatasSegundoB)-1]));
                    if($INISegundoB <= $DTHoje && $TERSegundoB >= $DTHoje){
                        $return['Conteudo'] = $Planejamento['segundoSemestre'];
                        $return['Estagio'] = "2º SEM";
                    }
                }
            break;
            case 'Anual':
                $arrIniPrimeiroB = [];
                $arrTerPrimeiroB = [];
                foreach($Planejamento['primeiroPeriodo'] as $pb){
                    array_push($arrIniPrimeiroB,$pb['Inicio']);
                    array_push($arrTerPrimeiroB,$pb['Termino']);
                }
                $ARRDatasPrimeiroB = array_merge($arrIniPrimeiroB,$arrTerPrimeiroB);
                if(count($ARRDatasPrimeiroB) > 0){
                    $INIPrimeiroB = Carbon::parse(self::alternativeUsData($ARRDatasPrimeiroB[0]));
                    $TERPrimeiroB = Carbon::parse(self::alternativeUsData($ARRDatasPrimeiroB[count($ARRDatasPrimeiroB)-1]));
                    if($INIPrimeiroB <= $DTHoje && $TERPrimeiroB >= $DTHoje){
                        $return['Conteudo'] = $Planejamento['primeiroPeriodo'];
                        $return['Estagio'] = "1º PER";
                    }
                }
            break;
        }
        ob_start();
        echo "<option value=''>Selecione</option>";
        echo "<option data-estagio='".$return['Estagio']."' value='PDF'>Fora do Sistema</option>";
        foreach($return['Conteudo'] as $r){
        ?>
        <optgroup label="<?=$r['Conteudo']." - ".$return['Estagio']?>">
        <?php
        foreach($r['Conteudos'] as $rc){
        ?>
        <option data-estagio="<?=$return['Estagio']?>" value="<?=$rc?>"><?=$rc?></option>
        <?php
            }
        ?>
        </optgroup>
        <?php
        }
        return ob_get_clean();
    }

    public function cadastro($id=null){
        $view = [
            'submodulos' => self::submodulos,
            'id' => ''
        ];

        if(Auth::user()->tipo == 6){
            $view['Disciplinas'] = EscolasController::getDisciplinasProfessor(Auth::user()->id);
        }else{
            $view['Disciplinas'] = EscolasController::getNomeDisciplinasEscola();
        }

        if($id){
            $rgs = self::getPlanejamento($id);
            $view['submodulos'] = self::cadastroSubmodulos;
            $view['id'] = $id;
            $view['Turmas'] = $rgs;
            $view['Registro'] = PlanejamentoAnual::find($id);
        }

        return view('Planejamentos.cadastro',$view);
    }

    public function cadastroAee($id=null){
        $IDEscolas = EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional);
        $view = [
            'submodulos' => self::submodulos,
            'id' => '',
            "Turmas" => Turma::whereIn("IDEscola",$IDEscolas)->get()
        ];

        if($id){
            $view['submodulos'] = self::aeeSubmodulos;
            $view['id'] = $id;
            $view['Registro'] = AEEPlanejamento::find($id);
        }

        return view('Planejamentos.cadastroAee',$view);
    }

    public function saveAee(Request $request){
        try{
            if($request->id){
                AEEPlanejamento::find($request->id)->update($request->all());
                $mensagem = 'Planejamento Alterado com Sucesso!';
                $rout = 'Planejamentos/AEE/Cadastro';
                $aid = $request->id;
            }else{
                $Planejamento = AEEPlanejamento::create($request->all());
                $mensagem = 'Planejamento Criado com Sucesso! Agora Crie os Conteúdos e Abordagens de cada Período';
                $rout = 'Planejamentos/AEE/Cadastro';
                $aid = $Planejamento->id;
            }
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro:'.$th->getMessage();
            $rout = 'Planejamentos/AEE/Novo';
            $aid = '';
            $status = 'error';
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function save(Request $request){
        try{
            if($request->id){
                PlanejamentoAnual::where('IDDisciplina',$request->IDDisciplina)->delete();
                foreach($request->Turma as $t){
                    PlanejamentoAnual::create([
                        'IDProfessor' => 0,
                        'IDDisciplina' => $request->IDDisciplina,
                        'NMPlanejamento' => $request->NMPlanejamento,
                        'IDTurma' => $t
                    ]);
                }

                $mensagem = 'Planejamento Alterado com Sucesso!';
                $rout = 'Planejamentos/Cadastro';
                $aid = $request->id;
            }else{
                
                foreach($request->Turma as $t){
                    $Planejamento = PlanejamentoAnual::create([
                        'IDProfessor' => 0,
                        'IDDisciplina' => $request->IDDisciplina,
                        'NMPlanejamento' => $request->NMPlanejamento,
                        'IDTurma' => $t
                    ]);
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

    public function getPlanejamentosAee(){
        $WHERE = " WHERE t.IDEscola IN(".implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)).")";
        $SQL = <<<SQL
            SELECT
                p.Nome,
                t.Serie,
                t.Nome as Turma,
                p.id
            FROM planejamento_aee p
            INNER JOIN turmas t ON(t.id = p.IDTurma)
            $WHERE
        SQL;
        $Planejamentos = DB::select($SQL);

        if(count($Planejamentos) > 0){
            foreach($Planejamentos as $p){
                $item = [];
                $item[] = $p->Nome;
                $item[] = $p->Turma;
                $item[] = "<a href='".route('Planejamentos/AEE/Cadastro',$p->id)."' class='btn btn-primary btn-xs'>Abrir</a>";
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

    public function getPlanejamentos(){
        $arrTurmasT = [];
        if(Auth::user()->tipo == 6){
            foreach(EscolasController::getDisciplinasProfessor(Auth::user()->id) as $gt){
                array_push($arrTurmasT,$gt->IDDisciplina);
            }
            $turmas = implode(",",$arrTurmasT);
        }else{
            $turmas  = implode(",",EscolasController::getDisciplinasEscola());
        }
        
        $orgId = Auth::user()->id_org;
        $SQL = <<<SQL
         SELECT 
            CASE WHEN
            	t.IDPlanejamento
            THEN
            	CONCAT('[', GROUP_CONCAT('"', t.Nome, '"' SEPARATOR ','), ']')
            END AS Turmas,
            pa.id as IDPlanejamento,
            pa.NMPlanejamento,
            t.Nome as Turma,
            t.Serie as Serie
        FROM planejamentoanual pa
        INNER JOIN turmas t ON(t.id = pa.IDTurma)
        INNER JOIN disciplinas d ON(d.id = pa.IDDisciplina)
        INNER JOIN escolas e ON(e.id = t.IDEscola)
        INNER JOIN organizacoes o ON(e.IDOrg = o.id)
        WHERE o.id = $orgId AND pa.IDDisciplina IN($turmas) GROUP BY pa.id
        SQL;
        //dd($SQL);

        $Planejamentos = DB::select($SQL);

        if(count($Planejamentos) > 0){
            foreach($Planejamentos as $p){
                $item = [];
                $item[] = $p->NMPlanejamento." - ".$p->Serie." ".$p->Turma;
                // $item[] = json_decode($p->Turmas);
                $item[] = "<a href='".route('Planejamentos/Cadastro',$p->IDPlanejamento)."' class='btn btn-primary btn-xs'>Abrir</a>";
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
