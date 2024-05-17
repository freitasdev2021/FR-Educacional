<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Escola;
use App\Models\Calendario;
use App\Models\Turmas;
use App\Models\Disciplina;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EscolasController extends Controller
{

    public const submodulos = array([
        "nome" => "Cadastro",
        "endereco" => "index",
        "rota" => "Escolas/index"
    ],[
        "nome" => "Anos Letivos",
        "endereco" => "Anosletivos",
        "rota" => "Escolas/Anosletivos"
    ],[
        "nome" => "Disciplinas",
        "endereco" => "Disciplinas",
        "rota" => "Escolas/Disciplinas"
    ],[
        "nome" => "Turmas",
        "endereco" => "Turmas",
        "rota" => "Escolas/Turmas"
    ]);

    public function index(){
        return view('Escolas.index',[
            "submodulos" => self::submodulos,
            'id' => ''
        ]);
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
            return redirect()->route('Escolas/Edit',$aid)->with($status,$mensagem);
        }
    }

    ////////////////////////////////////////////ANOS LETIVOS
    public function getAnosLetivos(){
        $anosletivos = DB::select("SELECT c.INIAno,c.TERAno,c.id,e.Nome FROM calendario c INNER JOIN escolas e ON(c.IDEscola = e.id) INNER JOIN organizacoes o ON(e.IDOrg = o.id) WHERE o.id = '".Auth::user()->id_org."'  ");

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
            $aid = '';
            $esc = $request->all();
            $esc['CEP'] = preg_replace('/\D/', '', $request->CEP);
            $esc['Telefone'] = preg_replace('/\D/', '', $request->Telefone);
            if($request->id){
                $Escola = Calendario::find($request->id);
                $Escola->update($esc);
                $rout = 'Escolas/Anosletivos/Cadastro';
                $aid = $request->id;
            }else{
                Calendario::create($esc);
                $rout = 'Escolas/Anosletivos/Novo';
            }
            $status = 'success';
            $mensagem = 'Salvamento Feito com Sucesso';
        }catch(\Throwable $th){
            $status = 'error';
            $mensagem = "Erro ao Salvar a Escola: ".$th;
        }finally{
            return redirect()->route('Escolas/Anosletivos/Cadastro',$aid)->with($status,$mensagem);
        }
    }
    ///////////////////////////////////////////DISCIPLINAS
    public function getDisciplinas(){
        $idorg = Auth::user()->id_org;

        $SQL = <<<SQL
        SELECT NMDisciplina, Obrigatoria,d.id, CONCAT('[', 
                GROUP_CONCAT(
                '"', e.Nome, '"' 
            SEPARATOR ','), 
        ']') as Escolas 
        FROM disciplinas d 
        INNER JOIN escolas e ON(d.IDEscola = e.id)
        INNER JOIN organizacoes o ON(e.IDOrg = o.id) WHERE o.id = $idorg
        GROUP BY NMDisciplina ;
        SQL;
        $disciplinas = DB::select($SQL);
        if(count($disciplinas) > 0){
            foreach($disciplinas as $d){
                $item = [];
                $item[] = $d->NMDisciplina;
                $item[] = implode(",",json_decode($d->Escolas));
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

        $view = [
            "submodulos" => self::submodulos,
            'id' => '',
            'escolas' => Escola::all()->where('IDOrg',Auth::user()->id_org)
        ];

        $idorg = Auth::user()->id_org;

        if($id){
            $SQL = <<<SQL
            SELECT 
                d.NMDisciplina, 
                d.Obrigatoria, 
                d.id, 
                (SELECT CONCAT('[', GROUP_CONCAT('"', e2.Nome, '"' SEPARATOR ','), ']')
                FROM escolas e2 
                WHERE e2.id IN 
                    (SELECT e3.id 
                    FROM disciplinas d3 INNER JOIN escolas e3 ON(e3.id = d3.IDEscola) 
                    WHERE d3.NMDisciplina = d.NMDisciplina)
                ) as Escolas
            FROM 
                disciplinas d
            INNER JOIN 
                escolas e ON (d.IDEscola = e.id)
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

    public function saveDisciplinas(Request $request){
        try{
            $aid = '';
            $esc = $request->all();
            //dd($esc['Escola']); 
            if($request->id){

                $SQL = <<<SQL
                SELECT 
                    d.NMDisciplina
                FROM disciplinas d
                WHERE d.id = $request->id
                SQL;

                $disciplinas = DB::select($SQL);

                //dd($disciplinas[0]->NMDisciplina);
                Disciplina::where('NMDisciplina',$disciplinas[0]->NMDisciplina)->delete();

                foreach($esc['Escola'] as $df){
                    $crieite = Disciplina::create([
                        "IDEscola" => $df,
                        "NMDisciplina" => $esc['NMDisciplina'],
                        "Obrigatoria" => $esc['Obrigatoria'] 
                    ]);
                }
                $rout = 'Escolas/Disciplinas/Cadastro';
                $aid = $crieite->id;
            }else{
                foreach($esc['Escola'] as $e){
                    Disciplina::create([
                        "IDEscola" => $e,
                        "NMDisciplina" => $esc['NMDisciplina'],
                        "Obrigatoria" => $esc['Obrigatoria'] 
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
    ///////////////////////////////////////////TURMAS
    public function getTurmas(){
        $SQL = <<<SQL
        SELECT NMDisciplina, Obrigatoria,d.id, CONCAT('[', 
                GROUP_CONCAT(
                '"', e.Nome, '"' 
            SEPARATOR ','), 
        ']') as Escolas 
        FROM disciplinas d 
        INNER JOIN escolas e ON(d.IDEscola = e.id) 
        INNER JOIN organizacoes o ON(e.IDOrg = o.id)
        GROUP BY NMDisciplina;
        SQL;

        $disciplinas = DB::select($SQL);
        if(count($disciplinas) > 0){
            foreach($disciplinas as $d){
                $item = [];
                $item[] = $d->NMDisciplina;
                $item[] = $d->IDEscola;
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

    public function turmas(){
        return view('Escolas.turmas',[
            "submodulos" => self::submodulos,
            'id' => ''
        ]);
    }

    public function cadastroTurmas($id=null){

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

        return view('Escolas.createAnoLetivo',$view);

    }

    public function saveTurmas(Request $request){
        try{
            $aid = '';
            $esc = $request->all();
            $esc['CEP'] = preg_replace('/\D/', '', $request->CEP);
            $esc['Telefone'] = preg_replace('/\D/', '', $request->Telefone);
            if($request->id){
                $Escola = Disciplina::find($request->id);
                $Escola->update($esc);
                $rout = 'Escolas/Anosletivos/Edit';
                $aid = $request->id;
            }else{
                Disciplina::create($esc);
                $rout = 'Escolas/Anosletivos/Novo';
            }
            $status = 'success';
            $mensagem = 'Salvamento Feito com Sucesso';
        }catch(\Throwable $th){
            $status = 'error';
            $mensagem = "Erro ao Salvar a Escola: ".$th;
        }finally{
            return redirect()->route('Escolas/Anosletivos/Edit',$aid)->with($status,$mensagem);
        }
    }
    /////////////////////////////////////////////

}
