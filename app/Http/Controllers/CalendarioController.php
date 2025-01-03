<?php

namespace App\Http\Controllers;
use App\Models\Escola;
use App\Models\FeriasAlunos;
use App\Models\Reuniao;
use App\Models\SabadoLetivo;
use App\Models\participacoesEvento;
use App\Models\Evento;
use App\Http\Controllers\SecretariasController;
use App\Models\Periodo;
use App\Models\Calendario;
use App\Models\CalendarioPlanejamento;
use App\Models\CalendarioRecuperacao;
use App\Http\Controllers\Services\CalendarController;
use App\Models\CalendarioFeriado;
use App\Models\Paralizacao;
use App\Models\FeriasProfissionais;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Support\Facades\Auth;
use DatePeriod;
use DateInterval;
use Storage;

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
        "nome" => "Afastamentos",
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
    ],[
        "nome" => "Recuperações",
        "endereco" => "Recuperacoes",
        "rota" => "Calendario/Recuperacoes"
    ],[
        "nome" => "Feriados",
        "endereco" => "Feriados",
        "rota" => "Calendario/Feriados"
    ],[
        "nome" => "Planejamento/Reuniões",
        "endereco" => "Planejamentos",
        "rota" => "Calendario/Planejamentos"
    ],[
        "nome" => "Periodos",
        "endereco" => "Periodos",
        "rota" => "Calendario/Periodos"
    ]);

    public function umAnoDepois()
    {
        // Data inicial - hoje
        $startDate = Carbon::parse(Calendario::whereYear('INIAno',date('Y'))->where('IDOrg', Auth::user()->id_org)->first()->INIAno);

        // Data final - daqui a um ano
        $endDate = Carbon::parse(Calendario::whereYear('TERAno',date('Y'))->where('IDOrg', Auth::user()->id_org)->first()->TERAno)->addDay();

        // Intervalo de 1 dia
        $interval = new DateInterval('P1D');

        // Cria o período de datas
        $period = new DatePeriod($startDate, $interval, $endDate);

        // Armazena as datas em um array
        $dates = [];
        foreach ($period as $date) {
            if($date->format('D') == 'Sat' && Carbon::parse($date)){
                $dates[] = $date->format('d/m/Y');
            }
        }

        // Exibe as datas (ou retorna como resposta JSON, ou qualquer outro uso que você desejar)
        return $dates;
    }

    public static function calendarioLetivo(){
        // Data inicial - hoje
        $startDate = Carbon::parse(Calendario::whereYear('INIAno',date('Y'))->where('IDOrg', Auth::user()->id_org)->first()->INIAno);

        // Data final - daqui a um ano
        $endDate = Carbon::parse(Calendario::whereYear('TERAno',date('Y'))->where('IDOrg', Auth::user()->id_org)->first()->TERAno)->addDay();

        // Intervalo de 1 dia
        $interval = new DateInterval('P1D');

        // Cria o período de datas
        $period = new DatePeriod($startDate, $interval, $endDate);

        // Armazena as datas em um array
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('d-m-Y');
        }

        // Exibe as datas (ou retorna como resposta JSON, ou qualquer outro uso que você desejar)
        return $dates;
    }

    public static function ferias(){
        $dates = [];
        $Ferias = FeriasAlunos::select('DTInicio','DTTermino')->whereIn('IDEscola',SecretariasController::getEscolasRede(Auth::user()->id_org))->whereYear('DTInicio',date('Y'))->get();

        foreach($Ferias as $f){
            array_push($dates,[
                'Inicio'=>$f->DTInicio,
                'Termino'=>$f->DTTermino
            ]);
        }

        return $dates;
    }

    public static function feriados(){
        $dates = [];
        $Recessos = CalendarioFeriado::select('DTInicio','DTTermino')->whereIn('IDEscola',SecretariasController::getEscolasRede(Auth::user()->id_org))->whereYear('DTInicio',date('Y'))->get();

        foreach($Recessos as $r){
            array_push($dates,[
                'Inicio'=>$r->DTInicio,
                'Termino'=>$r->DTTermino
            ]);
        }

        return $dates;
    }

    public function intervaloFeriados($calendario,$feriados){
        $intervalos = array();
        foreach($feriados as $f){
            $startDate = Carbon::parse($f['Inicio']);

            // Data final - daqui a um ano
            $endDate = Carbon::parse($f['Termino'])->addDay();
    
            // Intervalo de 1 dia
            $interval = new DateInterval('P1D');
    
            // Cria o período de datas
            $period = new DatePeriod($startDate, $interval, $endDate);
            foreach($period as $p){
                array_push($intervalos,$p->format('Y-m-d'));
            }
        }

        //DESCONTANDO DO CALENDARIO LETIVO
        foreach($intervalos as $in){
            $key = array_search($in, $calendario);
            if ($key !== false) {
                unset($calendario[$key]);
            }
        }

        return $calendario;
    }

    public static function recessos(){
        $dates = [];
        $Recessos = Paralizacao::select('DTInicio','DTTermino')->whereIn('IDEscola',SecretariasController::getEscolasRede(Auth::user()->id_org))->whereYear('DTInicio',date('Y'))->get();

        foreach($Recessos as $r){
            array_push($dates,[
                'Inicio'=>$r->DTInicio,
                'Termino'=>$r->DTTermino
            ]);
        }

        return $dates;
    }

    public function intervaloRecessos($calendario,$recessos){
        $intervalos = array();
        foreach($recessos as $f){
            $startDate = Carbon::parse($f['Inicio']);

            // Data final - daqui a um ano
            $endDate = Carbon::parse($f['Termino'])->addDay();
    
            // Intervalo de 1 dia
            $interval = new DateInterval('P1D');
    
            // Cria o período de datas
            $period = new DatePeriod($startDate, $interval, $endDate);
            foreach($period as $p){
                array_push($intervalos,$p->format('Y-m-d'));
            }
        }

        //DESCONTANDO DO CALENDARIO LETIVO
        foreach($intervalos as $in){
            $key = array_search($in, $calendario);
            if ($key !== false) {
                unset($calendario[$key]);
            }
        }

        return $calendario;
    }

    public function intervaloFerias($calendario,$ferias){
        $intervalos = array();
        foreach($ferias as $f){
            $startDate = Carbon::parse($f['Inicio']);

            // Data final - daqui a um ano
            $endDate = Carbon::parse($f['Termino'])->addDay();
    
            // Intervalo de 1 dia
            $interval = new DateInterval('P1D');
    
            // Cria o período de datas
            $period = new DatePeriod($startDate, $interval, $endDate);
            foreach($period as $p){
                array_push($intervalos,$p->format('Y-m-d'));
            }
        }

        //DESCONTANDO DO CALENDARIO LETIVO
        foreach($intervalos as $in){
            $key = array_search($in, $calendario);
            if ($key !== false) {
                unset($calendario[$key]);
            }
        }

        return $calendario;
    }

    public function index(){
        $idorg = Auth::user()->id_org;
        return view('Calendario.index',[
            "submodulos" => self::submodulos,
            'id' => '',
            'Escolas' => Escola::where('IDOrg',Auth::user()->id_org)->get(),
            "AnoLetivo" => DB::select("SELECT INIAno,TERAno,id as IDAno,INIRematricula,TERRematricula FROM calendario WHERE IDOrg = $idorg AND DATE_FORMAT(calendario.INIAno, '%Y') = DATE_FORMAT(NOW(),'%Y') ")
        ]);
    }

    public function feriadosIndex(){
        return view('Calendario.feriados',[
            "submodulos" => self::submodulos
        ]);
    }

    public function periodosIndex(){
        return view('Calendario.periodos',[
            "submodulos" => self::submodulos
        ]);
    }

    public function recuperacoesIndex(){
        return view('Calendario.recuperacoes',[
            "submodulos" => self::submodulos
        ]);
    }

    public function planejamentosIndex(){
        return view('Calendario.planejamentos',[
            "submodulos" => self::submodulos
        ]);
    }

    public function getRecuperacoes(){
        $idorg = Auth::user()->id_org;
        $AND = " AND e.id IN(".implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)).")";
        $AND .= " AND DATE_FORMAT(re.DTInicio, '%Y') = DATE_FORMAT(NOW(),'%Y')";

        $feriasAlunos = DB::select("SELECT re.Recuperacao,e.Nome as Escola, DTInicio,DTTermino,re.id as IDReco FROM calendario_recuperacoes re INNER JOIN escolas e ON(e.id = re.IDEscola) INNER JOIN organizacoes o ON(e.IDOrg = o.id) WHERE o.id = $idorg $AND ");

        if(count($feriasAlunos) > 0){
            foreach($feriasAlunos as $fa){
                $item = [];
                $item[] = $fa->Recuperacao;
                (in_array(Auth::user()->tipo,[2,2.5])) ? $item[] = $fa->Escola : '';
                $item[] = Controller::data($fa->DTInicio,'d/m/Y');
                $item[] = Controller::data($fa->DTTermino,'d/m/Y');
                (in_array(Auth::user()->tipo,[4,2])) ? $item[] = "<a href='".route('Calendario/Recuperacoes/Edit',$fa->IDReco)."' class='btn btn-primary btn-xs'>Editar</a>" : '';
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

    public function getFeriados(){
        $idorg = Auth::user()->id_org;
        $AND = " AND e.id IN(".implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)).")";
        $AND .= " AND DATE_FORMAT(fe.DTInicio, '%Y') = DATE_FORMAT(NOW(),'%Y')";

        $feriasAlunos = DB::select("SELECT fe.Feriado,e.Nome as Escola, DTInicio,DTTermino,fe.id as IDFer FROM calendario_feriados fe INNER JOIN escolas e ON(e.id = fe.IDEscola) INNER JOIN organizacoes o ON(e.IDOrg = o.id) WHERE o.id = $idorg $AND ");

        if(count($feriasAlunos) > 0){
            foreach($feriasAlunos as $fa){
                $item = [];
                $item[] = $fa->Feriado;
                (in_array(Auth::user()->tipo,[2,2.5])) ? $item[] = $fa->Escola : '';
                $item[] = Controller::data($fa->DTInicio,'d/m/Y')." - ".Controller::diaSemana($fa->DTInicio);
                $item[] = Controller::data($fa->DTTermino,'d/m/Y')." - ".Controller::diaSemana($fa->DTTermino);
                (in_array(Auth::user()->tipo,[4,2])) ? $item[] = "<a href='".route('Calendario/Feriados/Edit',$fa->IDFer)."' class='btn btn-primary btn-xs'>Editar</a>" : '';
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

    public function getPeriodos(){
        $idorg = Auth::user()->id_org;
        $AND = " AND e.id IN(".implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)).")";
        $AND .= " AND DATE_FORMAT(pe.DTInicio, '%Y') = DATE_FORMAT(NOW(),'%Y')";

        $periodos = DB::select("SELECT pe.Periodo,e.Nome as Escola, DTInicio,DTTermino,pe.id as IDPer FROM periodos pe INNER JOIN escolas e ON(e.id = pe.IDEscola) INNER JOIN organizacoes o ON(e.IDOrg = o.id) WHERE o.id = $idorg $AND ");

        if(count($periodos) > 0){
            foreach($periodos as $fa){
                $item = [];
                $item[] = $fa->Periodo;
                (in_array(Auth::user()->tipo,[2,2.5])) ? $item[] = $fa->Escola : '';
                $item[] = Controller::data($fa->DTInicio,'d/m/Y');
                $item[] = Controller::data($fa->DTTermino,'d/m/Y');
                (in_array(Auth::user()->tipo,[4,2])) ? $item[] = "<a href='".route('Calendario/Periodos/Edit',$fa->IDPer)."' class='btn btn-primary btn-xs'>Editar</a>" : '';
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($periodos)),
            "recordsFiltered" => intval(count($periodos)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }

    
    public function getPlanejamentos(){
        $idorg = Auth::user()->id_org;
        $AND = " AND e.id IN(".implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)).")";
        $AND .= " AND DATE_FORMAT(pl.DTInicio, '%Y') = DATE_FORMAT(NOW(),'%Y')";

        $feriasAlunos = DB::select("SELECT pl.Assunto,e.Nome as Escola, DTInicio,DTTermino,pl.id as IDPla FROM calendario_planejamentos pl INNER JOIN escolas e ON(e.id = pl.IDEscola) INNER JOIN organizacoes o ON(e.IDOrg = o.id) WHERE o.id = $idorg $AND ");

        if(count($feriasAlunos) > 0){
            foreach($feriasAlunos as $fa){
                $item = [];
                (in_array(Auth::user()->tipo,[2,2.5])) ? $item[] = $fa->Escola : '';
                $item[] = $fa->Assunto;
                $item[] = Controller::data($fa->DTInicio,'d/m/Y');
                $item[] = Controller::data($fa->DTTermino,'d/m/Y');
                (in_array(Auth::user()->tipo,[4,2])) ? $item[] = "<a href='".route('Calendario/Planejamentos/Edit',$fa->IDPla)."' class='btn btn-primary btn-xs'>Editar</a>" : '';
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

    public function cadastroFeriados($id=null){
        $orgId = Auth::user()->id_org;

        $view = [
            "submodulos" => self::submodulos,
            'id' => '',
            'Escolas' => Escola::all()->where('IDOrg',$orgId)
        ];

        if($id){
            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = "Calendario/Feriados/Edit";
            $view['id'] = $id;
            $view['Registro'] = CalendarioFeriado::find($id);
        }

        return view('Calendario.cadastroFeriados',$view);

    }

    public function cadastroPlanejamentos($id=null){
        $orgId = Auth::user()->id_org;

        $view = [
            "submodulos" => self::submodulos,
            'id' => '',
            'Escolas' => Escola::all()->where('IDOrg',$orgId)
        ];

        if($id){
            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = "Calendario/Planejamentos/Edit";
            $view['id'] = $id;
            $view['Registro'] = CalendarioPlanejamento::find($id);
        }

        return view('Calendario.cadastroPlanejamentos',$view);
    }

    public function cadastroPeriodos($id=null){
        $orgId = Auth::user()->id_org;

        $view = [
            "submodulos" => self::submodulos,
            'id' => '',
            'Escolas' => Escola::all()->where('IDOrg',$orgId)
        ];

        if($id){
            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = "Calendario/Periodos/Edit";
            $view['id'] = $id;
            $view['Registro'] = Periodo::find($id);
        }

        return view('Calendario.cadastroPeriodos',$view);
    }

    public function cadastroRecuperacoes($id=null){
        $orgId = Auth::user()->id_org;

        $view = [
            "submodulos" => self::submodulos,
            'id' => '',
            'Escolas' => Escola::all()->where('IDOrg',$orgId)
        ];

        if($id){
            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = "Calendario/Recuperacoes/Edit";
            $view['id'] = $id;
            $view['Registro'] = CalendarioRecuperacao::find($id);
        }

        return view('Calendario.cadastroRecuperacoes',$view);
    }

    public function eventosCadastro($id=null){
        $orgId = Auth::user()->id_org;

        $view = [
            "submodulos" => self::submodulos,
            'id' => '',
            'Escolas' => Escola::all()->where('IDOrg',$orgId)
        ];

        if($id){
            $SQL = <<<SQL
                SELECT 
                    ev.DSEvento,
                    ev.Inicio,
                    ev.Data,
                    es.id as IDEscola,
                    ev.Termino,
                    ev.id as IDEvento,
                    es.Nome as Escola 
                FROM 
                    eventos ev 
                    INNER JOIN escolas es ON(ev.IDEscola = es.id) 
                    INNER JOIN organizacoes o ON(o.id = es.IDOrg) 
                WHERE o.id = $orgId AND ev.id = $id
            SQL;
            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = "Calendario/Eventos/Edit";
            $view['id'] = $id;
            $view['Registro'] = DB::select($SQL)[0];
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

        $AND = " AND es.id IN(".implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)).")";

        $AND .= " AND DATE_FORMAT(ev.Data, '%Y') = DATE_FORMAT(NOW(),'%Y')";

        $SQL = <<<SQL
            SELECT 
                ev.DSEvento,
                ev.Inicio,
                ev.Termino,
                ev.Data,
                ev.id as IDEvento,
                es.Nome as Escola 
            FROM 
                eventos ev 
                INNER JOIN escolas es ON(ev.IDEscola = es.id) 
                INNER JOIN organizacoes o ON(o.id = es.IDOrg) 
            WHERE o.id = $idorg $AND
        SQL;

        $registros = DB::select($SQL);
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Data;
                $item[] = $r->DSEvento;
                $item[] = $r->Escola;
                $item[] = $r->Inicio;
                $item[] = $r->Termino;
                (in_array(Auth::user()->tipo,[4,2])) ? $item[] = "<a href='".route('Calendario/Eventos/Edit',$r->IDEvento)."' class='btn btn-primary btn-xs'>Editar</a>" : '';
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($registros)),
            "recordsFiltered" => intval(count($registros)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }

    public function saveEvento(Request $request){
        try{
            $aid = '';
            if($request->id){
                $Evento = Evento::find($request->id);
                $Evento->update($request->all());
                $rout = 'Calendario/Eventos/Edit';
                $mensagem = 'Salvamento Feito com Sucesso!';
                $aid = $request->id;
                ///
            }else{
                Evento::create($request->all());
                $aid = '';
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

    public function saveFeriados(Request $request){
        try{
            $aid = '';
            if($request->id){
                $Evento = CalendarioFeriado::find($request->id);
                $Evento->update($request->all());
                $rout = 'Calendario/Feriados/Edit';
                $mensagem = 'Salvamento Feito com Sucesso!';
                $aid = $request->id;
                ///
            }else{
                CalendarioFeriado::create($request->all());
                $aid = '';
                $rout = 'Calendario/Feriados/Novo';
                $mensagem = 'Salvamento Feito com Sucesso!';
            }
            $status = 'success';
        }catch(\Throwable $th){
            $status = 'error';
            $rout = 'Calendario/Feriados/Novo';
            $mensagem = "Erro ao Salvar: ".$th;
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function saveRecuperacoes(Request $request){
        try{
            $aid = '';
            if($request->id){
                $Evento = CalendarioRecuperacao::find($request->id);
                $Evento->update($request->all());
                $rout = 'Calendario/Recuperacoes/Edit';
                $mensagem = 'Salvamento Feito com Sucesso!';
                $aid = $request->id;
                ///
            }else{
                CalendarioRecuperacao::create($request->all());
                $aid = '';
                $rout = 'Calendario/Recuperacoes/Novo';
                $mensagem = 'Salvamento Feito com Sucesso!';
            }
            $status = 'success';
        }catch(\Throwable $th){
            $status = 'error';
            $mensagem = "Erro ao Salvar: ".$th;
            $rout = 'Calendario/Recuperacoes/Novo';
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function savePeriodos(Request $request){
        try{
            $aid = '';
            if($request->id){
                $Evento = Periodo::find($request->id);
                $Evento->update($request->all());
                $rout = 'Calendario/Periodos/Edit';
                $mensagem = 'Salvamento Feito com Sucesso!';
                $aid = $request->id;
                ///
            }else{
                Periodo::create($request->all());
                $aid = '';
                $rout = 'Calendario/Periodos/Novo';
                $mensagem = 'Salvamento Feito com Sucesso!';
            }
            $status = 'success';
        }catch(\Throwable $th){
            $status = 'error';
            $mensagem = "Erro ao Salvar: ".$th;
            $rout = 'Calendario/Periodos/Novo';
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function savePlanejamentos(Request $request){
        try{
            $aid = '';
            if($request->id){
                $Evento = CalendarioPlanejamento::find($request->id);
                $Evento->update($request->all());
                $rout = 'Calendario/Planejamentos/Edit';
                $mensagem = 'Salvamento Feito com Sucesso!';
                $aid = $request->id;
                ///
            }else{
                CalendarioPlanejamento::create($request->all());
                $aid = '';
                $rout = 'Calendario/Planejamentos/Novo';
                $mensagem = 'Salvamento Feito com Sucesso!';
            }
            $status = 'success';
        }catch(\Throwable $th){
            $status = 'error';
            $mensagem = "Erro ao Salvar: ".$th;
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
        $AND = " AND e.id IN(".implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)).")";

        $AND .= " AND DATE_FORMAT(fa.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y')";

        $feriasAlunos = DB::select("SELECT e.Nome as Escola, fa.DTInicio as Inicio, fa.DTTermino as Termino, fa.id as IDFerias FROM ferias_alunos fa INNER JOIN escolas e ON(e.id = fa.IDEscola) INNER JOIN organizacoes o ON(e.IDOrg = o.id) WHERE o.id = $idorg $AND ");

        if(count($feriasAlunos) > 0){
            foreach($feriasAlunos as $fa){
                $item = [];
                (in_array(Auth::user()->tipo,[2,2.5])) ? $item[] = $fa->Escola : '';
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

    public function diasLetivos(){
        $Dias = array();
        $AnoLetivo = self::calendarioLetivo();
        $FeriasAlunos = FeriasAlunos::whereYear('DTInicio',date('Y'))->select('DTInicio','DTTermino')->whereIn("IDEscola",SecretariasController::getEscolasRede(Auth::user()->id_org))->first();
        $sabadosLetivos = SabadoLetivo::whereYear('Data',date('Y'))->whereIn('IDEscola', SecretariasController::getEscolasRede(Auth::user()->id_org))->pluck('Data')->toArray();
        $feriados = CalendarioFeriado::whereYear('DTInicio',date('Y'))->whereIn('IDEscola', SecretariasController::getEscolasRede(Auth::user()->id_org))->pluck("DTInicio")->toArray();
        $recessos = Paralizacao::whereYear('DTInicio',date('Y'))->whereIn('IDEscola',SecretariasController::getEscolasRede(Auth::user()->id_org))->pluck('DTInicio')->toArray();
        $recuperacao = array_filter(
            array_map(function($ret) {
                // Converte a data para 'Y-m-d', removendo a hora
                return date('Y-m-d', strtotime($ret));
            }, CalendarioRecuperacao::whereYear('DTInicio',date('Y'))->whereIn('IDEscola', SecretariasController::getEscolasRede(Auth::user()->id_org))->pluck("DTInicio")->toArray()),
            function($ret) {
                // Aqui você pode aplicar uma condição adicional, se necessário
                return !empty($ret); // Exemplo de filtro para remover datas vazias
            }
        );
        
        foreach($AnoLetivo as $an){
            $Dia = Carbon::parse($an);

            if($Dia->format('D') != "Sat" && $Dia->format('D') != "Sun"){
                array_push($Dias,$Dia->format('Y-m-d'));
            }
        }

        $DiasLetivos = array_merge($Dias,array_unique($sabadosLetivos));

        usort($DiasLetivos,function($a,$b){
            return strtotime($a) - strtotime($b);
        });
        
        $DiasLetivos = self::intervaloFerias($DiasLetivos,self::ferias());
        $DiasLetivos = self::intervaloRecessos($DiasLetivos,self::recessos());
        $DiasLetivos = self::intervaloFeriados($DiasLetivos,self::feriados());

        dd($DiasLetivos);
    }

    public function getFeriasProfissionais(){

        $AND = " AND e.id IN(".implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)).")";

        $idorg = Auth::user()->id_org;
        $feriasProfissionais = DB::select("SELECT e.Nome as Escola, p.Nome as Professor,fp.DTInicio as Inicio, fp.DTTermino as Termino, fp.id as IDFerias FROM ferias_profissionais as fp INNER JOIN professores as p ON(fp.IDProfissional = p.id ) INNER JOIN escolas e ON(e.id = fp.IDEscola) INNER JOIN organizacoes o ON(e.IDOrg = o.id) WHERE o.id = $idorg $AND");

        if(count($feriasProfissionais) > 0){
            foreach($feriasProfissionais as $fp){
                $item = [];
                (in_array(Auth::user()->tipo,[2,2.5])) ? $item[] = $fp->Escola : '';
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
            $data = $request->all();
            if(Auth::user()->tipo == 4){
                $data['IDEscola'] = self::getEscolaDiretor(Auth::user()->id);
            }
            if($request->id){
                FeriasAlunos::find($request->id)->update($data);
                $aid = $request->id;
                $rout = 'Calendario/FeriasAlunos/Edit';
            }else{
                FeriasAlunos::create($data);
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

    public function gerarCalendario()
    {
        // Inicializando o FPDF
        $pdf = new FPDF();
       
        
        // Definindo os meses do ano
        $months = [
            'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
            'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
        ];

        // Definindo as cores para os tipos de dias
        $colors = [
            'letivo' => [200, 255, 200],    // Cor dos dias letivos
            'nao_letivo' => [255, 200, 200], // Cor dos dias não letivos
            'sabado_letivo' => [200, 200, 255], // Cor dos sábados letivos
            'exame_final' => [255, 255, 0], // Cor para exame final
        ];

        $FeriasAlunos = FeriasAlunos::select('DTInicio','DTTermino')->whereIn("IDEscola",SecretariasController::getEscolasRede(Auth::user()->id_org))->first();
        $sabadosLetivos = SabadoLetivo::whereIn('IDEscola', SecretariasController::getEscolasRede(Auth::user()->id_org))->pluck('Data')->toArray();
        $feriados = CalendarioFeriado::whereIn('IDEscola', SecretariasController::getEscolasRede(Auth::user()->id_org))->pluck("DTInicio")->toArray();
        $recessos = Paralizacao::whereIn('IDEscola',SecretariasController::getEscolasRede(Auth::user()->id_org))->pluck('DTInicio')->toArray();
        $recuperacao = array_filter(
            array_map(function($ret) {
                // Converte a data para 'Y-m-d', removendo a hora
                return date('Y-m-d', strtotime($ret));
            }, CalendarioRecuperacao::whereIn('IDEscola', SecretariasController::getEscolasRede(Auth::user()->id_org))->pluck("DTInicio")->toArray()),
            function($ret) {
                // Aqui você pode aplicar uma condição adicional, se necessário
                return !empty($ret); // Exemplo de filtro para remover datas vazias
            }
        );        
        
        $diasLetivos = array();
        //dd(self::calendarioLetivo());
        foreach(self::calendarioLetivo() as $dl){
            if(!in_array($dl,$recessos) || !in_array($dl,$feriados) || $dl->format('D') != "Sun" || $dl->format('D') == "Sat" && !in_array($dl,$sabadosLetivos)){
                array_push($diasLetivos,$dl);
            }
        }

        $intervaloFerias = [];
        dd($FeriasAlunos->DTInicio);
        // Feriados e sábados letivos fictícios
        $feriados = [5, 12, 19, 25];  // Exemplo de feriados
        $sabadosLetivos = [6, 13, 20, 27];  // Exemplo de sábados letivos
        $exames = [];
        $Ano = date('Y');
        
        // Gerando o conteúdo do calendário
        foreach ($months as $index => $month) {
            
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, "$month $Ano", 0, 1, 'C');
            $pdf->Ln();

            // Definindo a tabela de dias da semana
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(20, 10, 'Dom', 1, 0, 'C');
            $pdf->Cell(20, 10, 'Seg', 1, 0, 'C');
            $pdf->Cell(20, 10, 'Ter', 1, 0, 'C');
            $pdf->Cell(20, 10, 'Qua', 1, 0, 'C');
            $pdf->Cell(20, 10, 'Qui', 1, 0, 'C');
            $pdf->Cell(20, 10, 'Sex', 1, 0, 'C');
            $pdf->Cell(20, 10, 'Sab', 1, 1, 'C');

            // Obtendo o número de dias no mês
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $index + 1, 2024);
            
            // Obtendo o dia da semana do primeiro dia do mês
            $firstDayOfMonth = date('w', strtotime($Ano."-".($index+1)."-01"));

            // Definindo a fonte para os dias
            $pdf->SetFont('Arial', '', 10);

            // Preenchendo os dias do mês
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $sumMes = 1;
                dd($Ano."-".$index+$sumMes."-".$i);
                // Preenchendo as células vazias até o primeiro dia do mês
                if ($i == 1) {
                    for ($j = 0; $j < $firstDayOfMonth; $j++) {
                        $pdf->Cell(20, 10, '', 1);
                    }
                }

                // Definindo a cor de fundo para o dia
                if (in_array($i, $holidays)) {
                    $pdf->SetFillColor($colors['nao_letivo'][0], $colors['nao_letivo'][1], $colors['nao_letivo'][2]);
                } elseif (in_array($i, $saturdaysLetivos)) {
                    $pdf->SetFillColor($colors['sabado_letivo'][0], $colors['sabado_letivo'][1], $colors['sabado_letivo'][2]);
                } else {
                    $pdf->SetFillColor($colors['letivo'][0], $colors['letivo'][1], $colors['letivo'][2]);
                }

                // Exibindo o número do dia
                $pdf->Cell(20, 10, $i, 1, 0, 'C', true);

                // Quebra de linha no final de cada semana
                if (($firstDayOfMonth + $i) % 7 == 0) {
                    $pdf->Ln();
                }
            }

            $pdf->Ln(10);
        }

        // Gerando a legenda no final
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, 10, 'Legenda:', 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(10, 10, 'Letivo', 0, 0, 'L');
        $pdf->SetFillColor($colors['letivo'][0], $colors['letivo'][1], $colors['letivo'][2]);
        $pdf->Cell(10, 10, '', 0, 1, 'L', true);

        $pdf->Cell(10, 10, 'Nao Letivo', 0, 0, 'L');
        $pdf->SetFillColor($colors['nao_letivo'][0], $colors['nao_letivo'][1], $colors['nao_letivo'][2]);
        $pdf->Cell(10, 10, '', 0, 1, 'L', true);

        $pdf->Cell(10, 10, 'Sabado Letivo', 0, 0, 'L');
        $pdf->SetFillColor($colors['sabado_letivo'][0], $colors['sabado_letivo'][1], $colors['sabado_letivo'][2]);
        $pdf->Cell(10, 10, '', 0, 1, 'L', true);

        $pdf->Cell(10, 10, 'Exame Final', 0, 0, 'L');
        $pdf->SetFillColor($colors['exame_final'][0], $colors['exame_final'][1], $colors['exame_final'][2]);
        $pdf->Cell(10, 10, '', 0, 1, 'L', true);

        // Gerando o PDF e enviando para o navegador
        $pdf->Output('I', 'Calendario_Escolar_2024.pdf');
    }

    function saveFeriasProfissionais(Request $request){
        try{
            $status = 'success';
            $mensagem = 'Ferias Salvas com Sucesso';
            $data = $request->all();
            if($request->id){
                // if($request->file('Anexo')){
                //     $Anexo = $request->file('Anexo')->getClientOriginalName();
                //     Storage::disk('public')->delete('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'. $request->CDPasta . '/' . $request->oldAnexo);
                //     $request->file('Anexo')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$request->CDPasta,$Anexo,'public');
                // }else{
                //     $Anexo = '';
                // }
                FeriasProfissionais::find($request->id)->update($data);
                $aid = $request->id;
                $rout = 'Calendario/FeriasProfissionais/Edit';
            }else{
                // if($request->file('Anexo')){
                //     $Anexo = $request->file('Anexo')->getClientOriginalName();
                //     $request->file('Anexo')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$request->CDPasta,$Anexo,'public');
                // }else{
                //     $Anexo = '';
                // }
                FeriasProfissionais::create($data);
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

        $AND = " AND e.id IN(".implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)).")";

        $AND .= " AND DATE_FORMAT(s.Data, '%Y') = DATE_FORMAT(NOW(),'%Y')";

        $idorg = Auth::user()->id_org;
        $sabados = DB::select("SELECT e.Nome as Escola, s.Data as Sabado, s.id as IDSabado FROM sabados_letivos s INNER JOIN escolas e ON(e.id = s.IDEscola) INNER JOIN organizacoes o ON(e.IDOrg = o.id) WHERE o.id = $idorg $AND ");

        if(count($sabados) > 0){
            foreach($sabados as $s){
                $item = [];
                (in_array(Auth::user()->tipo,[2,2.5])) ? $item[] = $s->Escola : '' ;
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

        $AND = " AND e.id IN(".implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)).")";

        $AND .= " AND DATE_FORMAT(p.DTInicio, '%Y') = DATE_FORMAT(NOW(),'%Y')";

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
            $view['Registro'] = DB::select("SELECT e.id as IDEscola, p.id as IDProfessor,p.Nome as Professor,fp.DSAfastamento,fp.DTInicio as Inicio, fp.DTTermino as Termino, fp.id as IDFerias FROM ferias_profissionais as fp INNER JOIN professores as p ON(fp.IDProfissional = p.id ) INNER JOIN escolas e ON(e.id = fp.IDEscola) WHERE fp.id = $id")[0];
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

    public function reunioesIndex(){
        return view('Calendario.reunioes',[
            "submodulos" => self::submodulos,
            'id' => ''
        ]);
    }

    public function reunioesCadastro($id){
        $view = [
            'submodulos' => self::submodulos,
            'id' => '',
            'Escolas' => Escola::where('IDOrg',Auth::user()->id_org)->get()
        ];

        if($id){
            $view['id'] = $id;
            $view['Registro'] = Reuniao::find($id);
        }

        return view('Calendario.cadastroReunioes',$view);
    }
}
