<?php

namespace App\Http\Controllers;
use App\Models\Escola;
use App\Models\participacoesEvento;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CalendarioController extends Controller
{
    public const submodulos = array([
        "nome" => "Calendario Letivo",
        "endereco" => "index",
        "rota" => "Calendario/index"
    ],[
        "nome" => "Eventos",
        "endereco" => "Eventos",
        "rota" => "Calendario/Eventos"
    ],[
        "nome" => "Ferias Alunos",
        "endereco" => "FeriasAlunos",
        "rota" => "Calendario/FeriasAlunos"
    ],[
        "nome" => "Ferias Profissionais",
        "endereco" => "FeriasProfissionais",
        "rota" => "Calendario/FeriasProfissionais"
    ],[
        "nome" => "Sabados Letivos",
        "endereco" => "Sabados",
        "rota" => "Calendario/Sabados"
    ],[
        "nome" => "Paralizaçoes/Recessos",
        "endereco" => "Paralizacoes",
        "rota" => "Calendario/Paralizacoes"
    ]);

    public function index(){
        return view('Calendario.index',[
            "submodulos" => self::submodulos,
            'id' => '',
            'Escolas' => Escola::where('IDOrg',Auth::user()->id_org)->get()
        ]);
    }

    public function reunioesIndex(){
        return view('Calendario.reunioes',[
            "submodulos" => self::submodulos,
            'id' => '',
            'Escolas' => Escola::where('IDOrg',Auth::user()->id_org)->get()
        ]);
    }

    public function eventosCadastro($id=null){
        $orgId = Auth::user()->id_org;

        $view = [
            "submodulos" => self::submodulos,
            'id' => '',
            'EscolasRegistradas' => Controller::array_associative_unique(DB::select("SELECT e.id as IDEscola,e.Nome as Escola FROM escolas e INNER JOIN organizacoes o ON(e.IDOrg = o.id) WHERE o.id = $orgId ORDER BY e.Nome"))
        ];

        if($id){
            $SQL = <<<SQL
                SELECT 
                e.Nome Escola,
                    e.id as IDEscola,
                    pe.DTInicio as INITurno,
                    pe.DTTermino as TERTurno,
                    ev.DSEvento,
                    ev.id as IDEvento,
                CASE WHEN (SELECT COUNT(IDEscola) FROM participacoeseventos WHERE IDEvento = $id AND IDEscola = e.id) > 0 THEN 1 ELSE 0 END as Participando
                FROM escolas e 
                LEFT JOIN participacoeseventos pe ON(e.id = pe.IDEscola) 
                LEFT JOIN eventos ev ON(ev.id = pe.IDEvento)
                SQL;

                $view['submodulos'][0]['endereco'] = "Edit";
                $view['submodulos'][0]['rota'] = "Calendario/Eventos/Edit";
                $view['id'] = $id;
                $view['Registro'] = Evento::find($id);
                $view['EscolasRegistradas'] = Controller::array_associative_unique(DB::select($SQL));
        }

        return view('Calendario.cadastroEventos',$view);

    }

    public function eventosIndex(){
        return view('Calendario.eventos',[
            "submodulos" => self::submodulos,
            'id' => '',
            'Escolas' => Escola::where('IDOrg',Auth::user()->id_org)->get()
        ]);
    }

    public function getEventos(){
        $idorg = Auth::user()->id_org;
        $SQL = <<<SQL
        SELECT 
            CONCAT('[',
                        GROUP_CONCAT(
                        '{'
                        ,'"Escola":"',e.Nome,'"'
                        ,',"DTInicio":"',pe.DTInicio,'"'
                        ,',"DTTermino":"',pe.DTTermino,'"'
                        ,'}' 
                    SEPARATOR ','),
                ']') as Escolas,
                DSEvento,
                ev.id as IDEvento
        FROM eventos ev
        INNER JOIN participacoeseventos pe ON(ev.id = pe.IDEvento)
        INNER JOIN escolas e ON(e.id = pe.IDEscola)
        GROUP BY IDEvento
        SQL;

        $iscolas = [];

        $diretores = DB::select($SQL);
        if(count($diretores) > 0){
            foreach($diretores as $d){

                foreach(json_decode($d->Escolas,true) as $es){
                    array_push($iscolas,"<ul>".$es['Escola']."(".$es['DTInicio'].") - (".$es['DTTermino'].")"."</ul>");
                }

                $item = [];
                $item[] = $d->DSEvento;
                $item[] = implode(' ',array_unique($iscolas));
                $item[] = "<a href='".route('Calendario/Eventos/Edit',$d->IDEvento)."' class='btn btn-primary btn-xs'>Editar</a>";
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($diretores)),
            "recordsFiltered" => intval(count($diretores)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }

    public function saveEvento(Request $request){
        try{
            $aid = '';
            if($request->id){
                $Evento = Evento::find($request->id);
                $Evento->update([
                    'DSEvento' => $request->DSEvento
                ]);
                $rout = 'Calendario/Eventos/Edit';
                $mensagem = 'Salvamento Feito com Sucesso!';
                $aid = $request->id;
                ////
                if($request->alteraEvento){

                    $eventoParts = participacoesEvento::where('IDEvento',$request->id);
                    $eventoParts->delete();

                    $participacoes = [];
                    $DTInicio = [];
                    $DTTermino = [];
                    $escolas = $request->Escola;
    
                    foreach($request->DTInicio as $ini){
                        if(!is_null($ini)){
                            array_push($DTInicio,$ini);
                        }
                    }
    
                    foreach($request->DTTermino as $ter){
                        if(!is_null($ter)){
                            array_push($DTTermino,$ter);
                        }
                    }
    
                    for($i=0; $i<count($escolas);$i++){
                        $participacoes[] = [
                            'IDEvento' => $request->id,
                            'IDEscola' => $escolas[$i],
                            "DTInicio" => $DTInicio[$i],
                            "DTTermino" => $DTTermino[$i],
                            "DSEvento" => $request->DSEvento
                        ];
                    }
                    
                    foreach($participacoes as $pa){
                        participacoesEvento::create($pa);
                    }
                }
                ///
            }else{
                $participacoes = [];
                $DTInicio = [];
                $DTTermino = [];
                $escolas = $request->Escola;

                foreach($request->DTInicio as $ini){
                    if(!is_null($ini)){
                        array_push($DTInicio,$ini);
                    }
                }

                foreach($request->DTTermino as $ter){
                    if(!is_null($ter)){
                        array_push($DTTermino,$ter);
                    }
                }

                $evento = Evento::create([
                    "DSEvento" => $request->DSEvento
                ]);

                for($i=0; $i<count($escolas);$i++){
                    $participacoes[] = [
                        'IDEvento' => $evento->id,
                        'IDEscola' => $escolas[$i],
                        "DTInicio" => $DTInicio[$i],
                        "DTTermino" => $DTTermino[$i],
                        "DSEvento" => $request->DSEvento
                    ];
                }
                
                foreach($participacoes as $pa){
                    participacoesEvento::create($pa);
                }
                $rout = 'Calendario/Eventos/Novo';
                $mensagem = 'Salvamento Feito com Sucesso!';
            }
            $status = 'success';
        }catch(\Throwable $th){
            $status = 'error';
            $mensagem = "Erro ao Salvar a Escola: ".$th;
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function sabadosIndex(){
        return view('Calendario.sabados',[
            "submodulos" => self::submodulos,
            'id' => '',
            'Escolas' => Escola::where('IDOrg',Auth::user()->id_org)->get()
        ]);
    }

    public function feriasAlunosIndex(){
        return view('Calendario.feriasAlunos',[
            "submodulos" => self::submodulos,
            'id' => '',
            'Escolas' => Escola::where('IDOrg',Auth::user()->id_org)->get()
        ]);
    }

    public function feriasProfissionaisIndex(){
        return view('Calendario.feriasProfissionais',[
            "submodulos" => self::submodulos,
            'id' => '',
            'Escolas' => Escola::where('IDOrg',Auth::user()->id_org)->get()
        ]);
    }

    public function paralizacoesIndex(){
        return view('Calendario.paralizacoes',[
            "submodulos" => self::submodulos,
            'id' => '',
            'Escolas' => Escola::where('IDOrg',Auth::user()->id_org)->get()
        ]);
    }
}
