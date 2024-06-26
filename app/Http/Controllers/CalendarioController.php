<?php

namespace App\Http\Controllers;
use App\Models\Escola;
use App\Models\FeriasAlunos;
use App\Models\SabadoLetivo;
use App\Models\participacoesEvento;
use App\Models\Evento;
use App\Models\Calendario;
use App\Models\Paralizacao;
use App\Models\FeriasProfissionais;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use DatePeriod;
use DateInterval;

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
        "nome" => "Recessos",
        "endereco" => "Paralizacoes",
        "rota" => "Calendario/Paralizacoes"
    ]);

    public function umAnoDepois()
    {
        // Data inicial - hoje
        $startDate = Carbon::parse(Calendario::where('IDOrg', Auth::user()->id_org)->first()->INIAno);

        // Data final - daqui a um ano
        $endDate = Carbon::parse(Calendario::where('IDOrg', Auth::user()->id_org)->first()->TERAno)->addDay();

        // Intervalo de 1 dia
        $interval = new DateInterval('P1D');

        // Cria o período de datas
        $period = new DatePeriod($startDate, $interval, $endDate);

        // Armazena as datas em um array
        $dates = [];
        foreach ($period as $date) {
            if($date->format('D') == 'Sat' && Carbon::parse($date) > Carbon::now()){
                $dates[] = $date->format('d/m/Y');
            }
        }

        // Exibe as datas (ou retorna como resposta JSON, ou qualquer outro uso que você desejar)
        return $dates;
    }

    public function index(){
        $idorg = Auth::user()->id_org;
        return view('Calendario.index',[
            "submodulos" => self::submodulos,
            'id' => '',
            'Escolas' => Escola::where('IDOrg',Auth::user()->id_org)->get(),
            "AnoLetivo" => DB::select("SELECT INIAno,TERAno,id as IDAno FROM calendario WHERE IDOrg = $idorg ")
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

        if(Auth::user()->tipo == 4){
            $IDEscola = self::getEscolaDiretor(Auth::user()->id);
            $AND = ' AND e.id='.$IDEscola;
            //dd($AND);
        }else{
            $AND = '';
        }

        if(Auth::user()->tipo == 6){
            $AND .= " AND e.id IN(".implode(',',self::getCurrentEscolasProfessor(Auth::user()->id)).")";
        }else{
            $AND .=' ';
        }

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
        INNER JOIN organizacoes o ON(o.id = e.IDOrg)
        WHERE o.id = $idorg $AND
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
                (in_array(Auth::user()->tipo,[4,2])) ? $item[] = "<a href='".route('Calendario/Eventos/Edit',$d->IDEvento)."' class='btn btn-primary btn-xs'>Editar</a>" : '';
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

    public function saveSabados(Request $request){
        try{
            $status = 'success';
            $mensagem = 'Ferias Salvas com Sucesso';
            $dataSabado = Carbon::createFromFormat('d/m/Y',$request->Data);
            $sabadoLetivo = $request->all();
            $sabadoLetivo['Data'] = $dataSabado->format('Y-m-d');
            if($request->id){
                SabadoLetivo::find($request->id)->update($sabadoLetivo);
                $aid = $request->id;
                $rout = 'Calendario/Sabados/Edit';
            }else{
                SabadoLetivo::create($sabadoLetivo);
                $rout = 'Calendario/Sabados';
                $aid = '';
            }
        }catch(\Throwable $th){
            $status = 'error';
            $rout = 'Calendario/Sabados/Novo';
            $mensagem = 'Houve um Erro: '.$th;
            $aid = '';
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

    public function getFeriasAlunos(){
        $idorg = Auth::user()->id_org;
        if(Auth::user()->tipo == 4){
            $IDEscola = self::getEscolaDiretor(Auth::user()->id);
            $AND = ' AND e.id='.$IDEscola;
            //dd($AND);
        }else{
            $AND = '';
        }

        if(Auth::user()->tipo == 6){
            $AND .= " AND e.id IN(".implode(',',self::getCurrentEscolasProfessor(Auth::user()->id)).")";
        }else{
            $AND .=' ';
        }

        $feriasAlunos = DB::select("SELECT e.Nome as Escola, fa.DTInicio as Inicio, fa.DTTermino as Termino, fa.id as IDFerias FROM ferias_alunos fa INNER JOIN escolas e ON(e.id = fa.IDEscola) INNER JOIN organizacoes o ON(e.IDOrg = o.id) WHERE o.id = $idorg $AND ");

        if(count($feriasAlunos) > 0){
            foreach($feriasAlunos as $fa){
                $item = [];
                (Auth::user()->tipo == 2) ? $item[] = $fa->Escola : '';
                $item[] = Controller::data($fa->Inicio,'d/m/Y');
                $item[] = Controller::data($fa->Termino,'d/m/Y');
                (in_array(Auth::user()->tipo,[4,2])) ? $item[] = "<a href='".route('Calendario/FeriasAlunos/Edit',$fa->IDFerias)."' class='btn btn-primary btn-xs'>Editar</a>" : '';
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($feriasAlunos)),
            "recordsFiltered" => intval(count($feriasAlunos)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }

    public function getFeriasProfissionais(){

        if(Auth::user()->tipo == 4){
            $IDEscola = self::getEscolaDiretor(Auth::user()->id);
            $AND = ' AND e.id='.$IDEscola;
            //dd($AND);
        }else{
            $AND = '';
        }

        if(Auth::user()->tipo == 6){
            $AND .= " AND e.id IN(".implode(',',self::getCurrentEscolasProfessor(Auth::user()->id)).")";
        }else{
            $AND .=' ';
        }

        $idorg = Auth::user()->id_org;
        $feriasProfissionais = DB::select("SELECT e.Nome as Escola, p.Nome as Professor,fp.DTInicio as Inicio, fp.DTTermino as Termino, fp.id as IDFerias FROM ferias_profissionais as fp INNER JOIN professores as p ON(fp.IDProfissional = p.id ) INNER JOIN escolas e ON(e.id = fp.IDEscola) INNER JOIN organizacoes o ON(e.IDOrg = o.id) WHERE o.id = $idorg $AND");

        if(count($feriasProfissionais) > 0){
            foreach($feriasProfissionais as $fp){
                $item = [];
                (Auth::user()->tipo == 2) ? $item[] = $fp->Escola : '';
                $item[] = $fp->Professor;
                $item[] = Controller::data($fp->Inicio,'d/m/Y');
                $item[] = Controller::data($fp->Termino,'d/m/Y');
                (in_array(Auth::user()->tipo,[4,2])) ? $item[] = "<a href='".route('Calendario/FeriasProfissionais/Edit',$fp->IDFerias)."' class='btn btn-primary btn-xs'>Editar</a>" : '';
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($feriasProfissionais)),
            "recordsFiltered" => intval(count($feriasProfissionais)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }

    function saveFeriasAlunos(Request $request){
        try{
            $status = 'success';
            $mensagem = 'Ferias Salvas com Sucesso';
            if($request->id){
                FeriasAlunos::find($request->id)->update($request->all());
                $aid = $request->id;
                $rout = 'Calendario/FeriasAlunos/Edit';
            }else{
                FeriasAlunos::create($request->all());
                $rout = 'Calendario/FeriasAlunos';
                $aid = '';
            }
        }catch(\Throwable $th){
            $status = 'error';
            $rout = 'Calendario/FeriasAlunos/Novo';
            $mensagem = 'Houve um Erro: '.$th;
            $aid = '';
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    function saveFeriasProfissionais(Request $request){
        try{
            $status = 'success';
            $mensagem = 'Ferias Salvas com Sucesso';
            if($request->id){
                FeriasProfissionais::find($request->id)->update($request->all());
                $aid = $request->id;
                $rout = 'Calendario/FeriasProfissionais/Edit';
            }else{
                FeriasProfissionais::create($request->all());
                $rout = 'Calendario/FeriasProfissionais';
                $aid = '';
            }
        }catch(\Throwable $th){
            $status = 'error';
            $rout = 'Calendario/FeriasProfissionais/Novo';
            $mensagem = 'Houve um Erro: '.$th;
            $aid = '';
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function getSabados(){

        if(Auth::user()->tipo == 4){
            $IDEscola = self::getEscolaDiretor(Auth::user()->id);
            $AND = ' AND e.id='.$IDEscola;
            //dd($AND);
        }else{
            $AND = '';
        }

        if(Auth::user()->tipo == 6){
            $AND .= " AND e.id IN(".implode(',',self::getCurrentEscolasProfessor(Auth::user()->id)).")";
        }else{
            $AND .=' ';
        }

        $idorg = Auth::user()->id_org;
        $sabados = DB::select("SELECT e.Nome as Escola, s.Data as Sabado, s.id as IDSabado FROM sabados_letivos s INNER JOIN escolas e ON(e.id = s.IDEscola) INNER JOIN organizacoes o ON(e.IDOrg = o.id) WHERE o.id = $idorg $AND ");

        if(count($sabados) > 0){
            foreach($sabados as $s){
                $item = [];
                (Auth::user()->tipo == 2) ? $item[] = $s->Escola : '' ;
                $item[] = Controller::data($s->Sabado,'d/m/Y');
                (in_array(Auth::user()->tipo,[4,2])) ? $item[] = "<a href='".route('Calendario/Sabados/Edit',$s->IDSabado)."' class='btn btn-primary btn-xs'>Editar</a>" : '';
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($sabados)),
            "recordsFiltered" => intval(count($sabados)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }

    public function cadastroParalizacao($id=null){
        $view = [
            'submodulos' => self::submodulos,
            'id' => '',
            'Escolas' => Escola::where('IDOrg',Auth::user()->id_org)->get()
        ];

        $idorg = Auth::user()->id_org;

        if($id){
            $view['id'] = $id;
            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = 'Calendario/Paralizacoes/Edit';
            $view['Registro'] = DB::select("SELECT p.id as IDParalizacao, e.Nome as Escola, p.DTInicio as Inicio, p.DTTermino as Termino,p.DSMotivo,e.id as IDEscola FROM paralizacoes p INNER JOIN escolas e ON(e.id = p.IDEscola) INNER JOIN organizacoes o ON(o.id = e.IDOrg) WHERE o.id = $idorg AND p.id = $id")[0];
        }

        return view('Calendario.cadastroParalizacao',$view);
    }

    public function getParalizacoes(){
        $idorg = Auth::user()->id_org;

        if(Auth::user()->tipo == 4){
            $AND = " AND e.id =".self::getEscolaDiretor(Auth::user()->id);
        }else{
            $AND =' ';
        }

        if(Auth::user()->tipo == 6){
            $AND .= " AND e.id IN(".implode(',',self::getCurrentEscolasProfessor(Auth::user()->id)).")";
        }else{
            $AND .=' ';
        }

        $paralizacao = DB::select("SELECT p.id as IDParalizacao, e.Nome as Escola, p.DTInicio as Inicio, p.DTTermino as Termino,p.DSMotivo FROM paralizacoes p INNER JOIN escolas e ON(e.id = p.IDEscola) INNER JOIN organizacoes o ON(o.id = e.IDOrg) WHERE o.id = $idorg $AND");
        if(count($paralizacao) > 0){
            foreach($paralizacao as $p){
                $item = [];
                $item[] = $p->Escola;
                $item[] = $p->DSMotivo;
                $item[] = Controller::data($p->Inicio,'d/m/Y');
                $item[] = Controller::data($p->Termino,'d/m/Y');
                (in_array(Auth::user()->tipo,[4,2])) ? $item[] = "<a href='".route('Calendario/Paralizacoes/Edit',$p->IDParalizacao)."' class='btn btn-primary btn-xs'>Editar</a>" : '';
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($paralizacao)),
            "recordsFiltered" => intval(count($paralizacao)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }

    public function saveParalizacao(Request $request){
        try{
            if($request->id){
                Paralizacao::find($request->id)->update($request->all());
                $rout = 'Calendario/Paralizacoes/Edit';
                $aid = $request->id;
            }else{
                Paralizacao::create($request->all());
                $aid = '';
                $rout = 'Calendario/Paralizacoes/Novo';
            }
            $mensagem = 'Salvamento feito com Sucesso';
            $status = 'success';
        }catch(\Throwable $th){
            $aid = '';
            $status = 'error';
            $mensagem = 'Houve um Erro ao Salvar a Paralizacao '.$th;
            $rout = 'Calendario/Paralizacoes/Novo';
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function cadastroFeriasAlunos($id= null){
        $view = [
            "submodulos" => self::submodulos,
            'id' => '',
            'Escolas' => Escola::where('IDOrg',Auth::user()->id_org)->get()
        ];

        $idorg = Auth::user()->id_org;

        if($id){
            $feriasAlunos = DB::select("SELECT e.id as IDEscola, fa.DTInicio as Inicio, fa.DTTermino as Termino, fa.id as IDFerias FROM ferias_alunos fa INNER JOIN escolas e ON(e.id = fa.IDEscola) INNER JOIN organizacoes o ON(e.IDOrg = o.id) WHERE o.id = 1 AND fa.id = $id ");
            $view['id'] = $id;
            $view['Registro']= $feriasAlunos[0];
        }

        return view('Calendario.cadastroFeriasAluno',$view);
    }

    public function cadastroFeriasProfissionais($id=null){
        $idorg = Auth::user()->id_org;
        $view = [
            "submodulos" => self::submodulos,
            'id' => '',
            'Escolas' => Escola::where('IDOrg',Auth::user()->id_org)->get(),
            'Professores' => DB::select("SELECT p.id as IDProfessor, p.Nome as Professor FROM professores p INNER JOIN alocacoes a ON(p.id = a.IDProfissional) INNER JOIN escolas e ON(a.IDEscola = e.id) INNER JOIN organizacoes o ON(o.id = e.IDOrg) WHERE o.id = $idorg GROUP BY a.IDProfissional ")
        ];

        $idorg = Auth::user()->id_org;

        if($id){
            $view['id'] = $id;
            $view['Registro'] = DB::select("SELECT e.id as IDEscola, p.id as IDProfessor,p.Nome as Professor,fp.DTInicio as Inicio, fp.DTTermino as Termino, fp.id as IDFerias FROM ferias_profissionais as fp INNER JOIN professores as p ON(fp.IDProfissional = p.id ) INNER JOIN escolas e ON(e.id = fp.IDEscola) WHERE fp.id = $id")[0];
        }

        return view('Calendario.cadastroFeriasProfissionais',$view);
    }



    public function cadastroSabados($id= null){
        $view = [
            "submodulos" => self::submodulos,
            'id' => '',
            'Escolas' => Escola::where('IDOrg',Auth::user()->id_org)->get(),
            'Sabados' => self::umAnoDepois()
        ];

        $idorg = Auth::user()->id_org;

        if($id){
            $sabado = DB::select("SELECT e.id as IDEscola, s.Data as Sabado, s.id as IDSabado FROM sabados_letivos s INNER JOIN escolas e ON(e.id = s.IDEscola) INNER JOIN organizacoes o ON(e.IDOrg = o.id) WHERE o.id = $idorg AND s.id = $id ");
            $view['id'] = $id;
            $view['Registro'] = $sabado[0];
        }

        return view('Calendario.cadastroSabado',$view);
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
