<?php

namespace App\Http\Controllers;
use App\Models\Escola;
use App\Models\FeriasAlunos;
use App\Models\Reuniao;
use App\Models\SabadoLetivo;
use App\Models\participacoesEvento;
use App\Models\Evento;
use App\Models\Periodo;
use App\Models\Calendario;
use App\Models\CalendarioPlanejamento;
use App\Models\CalendarioRecuperacao;
use App\Models\CalendarioFeriado;
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
            "AnoLetivo" => DB::select("SELECT INIAno,TERAno,id as IDAno,INIRematricula,TERRematricula FROM calendario WHERE IDOrg = $idorg ")
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
        if(Auth::user()->tipo == 4){
            $IDEscola = self::getEscolaDiretor(Auth::user()->id);
            $AND = ' AND e.id='.$IDEscola;
            //dd($AND);
        }else{
            $AND = '';
        }

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
        if(Auth::user()->tipo == 4){
            $IDEscola = self::getEscolaDiretor(Auth::user()->id);
            $AND = ' AND e.id='.$IDEscola;
            //dd($AND);
        }else{
            $AND = '';
        }

        $feriasAlunos = DB::select("SELECT fe.Feriado,e.Nome as Escola, DTInicio,DTTermino,fe.id as IDFer FROM calendario_feriados fe INNER JOIN escolas e ON(e.id = fe.IDEscola) INNER JOIN organizacoes o ON(e.IDOrg = o.id) WHERE o.id = $idorg $AND ");

        if(count($feriasAlunos) > 0){
            foreach($feriasAlunos as $fa){
                $item = [];
                $item[] = $fa->Feriado;
                (in_array(Auth::user()->tipo,[2,2.5])) ? $item[] = $fa->Escola : '';
                $item[] = Controller::data($fa->DTInicio,'d/m/Y');
                $item[] = Controller::data($fa->DTTermino,'d/m/Y');
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
        if(Auth::user()->tipo == 4){
            $IDEscola = self::getEscolaDiretor(Auth::user()->id);
            $AND = ' AND e.id='.$IDEscola;
            //dd($AND);
        }else{
            $AND = '';
        }

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
        if(Auth::user()->tipo == 4){
            $IDEscola = self::getEscolaDiretor(Auth::user()->id);
            $AND = ' AND e.id='.$IDEscola;
            //dd($AND);
        }else{
            $AND = '';
        }

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

        if(Auth::user()->tipo == 4){
            $IDEscola = self::getEscolaDiretor(Auth::user()->id);
            $AND = ' AND es.id='.$IDEscola;
            //dd($AND);
        }else{
            $AND = '';
        }

        if(Auth::user()->tipo == 6){
            $AND .= " AND es.id IN(".implode(',',self::getCurrentEscolasProfessor(Auth::user()->id)).")";
        }else{
            $AND .=' ';
        }

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
