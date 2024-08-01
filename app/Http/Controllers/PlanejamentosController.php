<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Turma;
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
        $rgs = DB::select("SELECT pa.PLConteudos,t.Periodo FROM planejamentoanual pa INNER JOIN turmas t ON(t.id = pa.IDTurma) WHERE pa.id = $id")[0];
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

    public function getPlanejamentoByTurma($IDDisciplina){
        $SQL = DB::select("SELECT pa.PLConteudos,t.Periodo FROM planejamentoanual pa INNER JOIN turmas t ON(t.id = pa.IDTurma) WHERE IDDisciplina = $IDDisciplina ")[0];
        $Planejamento = json_decode(json_decode($SQL->PLConteudos,true),true);
        $DTHoje = date('Y-m-d');
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
                    if($INIPrimeiroB <= $DTHoje && $TERPrimeiroB >= $DTHoje){
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
        foreach($return['Conteudo'] as $r){
        ?>
        <optgroup label="<?=$r['Conteudo']?>">
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
            'id' => '',
            'Disciplinas' => EscolasController::getDisciplinasProfessor(Auth::user()->id)
        ];

        if($id){
            $rgs = self::getPlanejamento($id);
            $view['submodulos'] = self::cadastroSubmodulos;
            $view['id'] = $id;
            $view['Turmas'] = $rgs;
            $view['Registro'] = PlanejamentoAnual::find($id);
        }

        return view('Planejamentos.cadastro',$view);
    }

    public function save(Request $request){
        try{
            if($request->id){
                PlanejamentoAnual::where('IDDisciplina',$request->IDDisciplina)->delete();
                foreach($request->Turma as $t){
                    PlanejamentoAnual::create([
                        'IDProfessor' => ProfessoresController::getProfessorByUser(Auth::user()->id),
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
                        'IDProfessor' => ProfessoresController::getProfessorByUser(Auth::user()->id),
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

    public function getPlanejamentos(){
        $arrTurmasT = [];
        foreach(EscolasController::getDisciplinasProfessor(Auth::user()->id) as $gt){
            array_push($arrTurmasT,$gt->IDDisciplina);
        }
        $turmas = implode(",",$arrTurmasT);
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
        INNER JOIN turmas t ON(t.id = pa.IDTurma)
        INNER JOIN disciplinas d ON(d.id = pa.IDDisciplina)
        INNER JOIN escolas e ON(e.id = t.IDEscola)
        INNER JOIN organizacoes o ON(e.IDOrg = o.id)
        WHERE o.id = $orgId AND pa.id IN($turmas) GROUP BY pa.id
        SQL;
        //dd($SQL);

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
