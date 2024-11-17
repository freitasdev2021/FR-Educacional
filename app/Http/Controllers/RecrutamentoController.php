<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CandidatoRequest;
use App\Models\Candidato;
use App\Models\CandidatoAnexo;
use App\Models\CandidatoCurso;
use App\Models\User;
use App\Models\Inscricao;
use Illuminate\Auth\Events\Registered;
use App\Models\ProcessoSeletivo;
use App\Http\Controllers\SecretariasController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RecrutamentoController extends Controller
{
    public const submodulos = array([
        "nome" => "Cadastro",
        "rota" => "Recrutamento/index",
        "endereco" =>"index"
    ]);

    public function index(){
        return view('Recrutamento.index',[
            "submodulos" => self::submodulos,
            "Org" => SecretariasController::getOrg(Auth::user()->id_org)
        ]);
    }

    public function inscrever($id){
        $IDUser = Auth::user()->id;
        $Candidatura = Candidato::where('IDUser',$IDUser)->first();
        $IDCandidato = $Candidatura->id;

        $Cursos = CandidatoCurso::where('IDCandidato', $IDCandidato)->get();
        $Anexos = CandidatoAnexo::where('IDCandidato', $IDCandidato)->where('Tipo', 'Certificado')->get();

        // Inicializando pontuação
        $Pontuacao = 0;

        // Definindo valores de pontuação para cada tipo de curso
        $pontosPorCurso = [
            "Técnico" => 5,
            "Pós-Graduação" => 15,
            "Mestrado" => 20,
            "Superior" => 10,
            "Doutorado" => 25,
            "MBA" => 30,
            "PhD" => 35,
        ];

        // Calculando pontuação com base nos cursos
        foreach ($Cursos as $curso) {
            $tipoCurso = $curso->Tipo;

            if (array_key_exists($tipoCurso, $pontosPorCurso)) {
                $Pontuacao += $pontosPorCurso[$tipoCurso];
            }
        }

        // Calculando pontuação com base nos anexos
        foreach ($Anexos as $anexo) {
            $Pontuacao += 5; // Cada certificado vale 5 pontos
        }

        Inscricao::create([
            "IDCandidato" => $IDCandidato,
            "IDProcesso" => $id,
            "Pontuacao" => $Pontuacao
        ]);

        return redirect()->back();
    }

    public function save(Request $request){
        try{
            $data = $request->all();
            $data['IDOrg'] = Auth::user()->id_org;

            if($request->id){
                ProcessoSeletivo::find($request->id)->update($data);
                $rota = 'Recrutamento/Edit';
                $aid = $request->id;
            }else{
                ProcessoSeletivo::create($data);
                $aid = '';
                $rota = 'Recrutamento/Novo';
            }
            $mensagem = "Salvamento Feito com Sucesso!";
            $status = 'success';
        }catch(\Throwable $th){
            $rota = 'Escolas/Salas/Novo';
            $mensagem = 'Erro '.$th;
            $aid = '';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function saveAnexo(Request $request){
        try{
            $data = $request->all();
            $data['IDCandidato'] = $request->IDCandidato;
            
            if($request->file('Anexo')){
                $Anexo = $request->file('Anexo')->getClientOriginalName();
                $request->file('Anexo')->storeAs('organizacao_'.Auth::user()->id_org.'_candidatos/candidato_'.$request->IDCandidato,$Anexo,'public');
            }else{
                $Anexo = '';
            }
            $data['Anexo'] = $Anexo;

            CandidatoAnexo::create($data);
            $aid = '';
            $rota = 'Candidatura/index';
            $mensagem = "Salvamento Feito com Sucesso!";
            $status = 'success';
        }catch(\Throwable $th){
            $rota = 'Candidatura/index';
            $mensagem = 'Erro '.$th;
            $aid = '';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function saveCurso(Request $request){
        try{
            $data = $request->all();
            $data['IDCandidato'] = $request->IDCandidato;

            CandidatoCurso::create($data);
            $aid = '';
            $rota = 'Candidatura/index';
            $mensagem = "Salvamento Feito com Sucesso!";
            $status = 'success';
        }catch(\Throwable $th){
            $rota = 'Candidatura/index';
            $mensagem = 'Erro '.$th;
            $aid = '';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function candidatura(){
        $IDUser = Auth::user()->id;
        $Candidatura = Candidato::where('IDUser',$IDUser)->first();
        $IDCandidato = $Candidatura->id;

        $Cursos = CandidatoCurso::where('IDCandidato',$IDCandidato)->get();
        $Anexos = CandidatoAnexo::where('IDCandidato',$IDCandidato)->get();
        return view("Recrutamento.perfil",[
            "submodulos" => self::submodulos,
            "Registro" => $Candidatura,
            "Anexos" => $Anexos,
            "Cursos" => $Cursos
        ]);
    }

    public function saveCandidatura(Request $request){
        $IDUser = Auth::user()->id;
        $Candidatura = Candidato::where('IDUser',$IDUser)->first();
        $Candidatura->update($request->all());
        return redirect()->back();
    }

    public function registrar(CandidatoRequest $request,$ID){;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tipo' => 8,
            'id_org' => $ID
        ]);

        Candidato::create([
            "IDUser" => $user->id,
            "Nome" => $request->name,
            "IDOrg" => $ID,
            "Escolaridade" => $request->Escolaridade,
            "Telefone" => $request->Telefone,
            "Nascimento" => $request->Nascimento,
            "Email" => $request->email
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    public function registro($Orgao,$ID){
        return view('Recrutamento.registro',[
            "IDOrg" => $ID,
            "Orgao" => $Orgao
        ]);
    }

    public function cadastro($id=null){
        $view = array(
            "submodulos" => self::submodulos,
            "id" => ''
        );

        if($id){
            $view['id'] = $id;
            $view['Registro'] = ProcessoSeletivo::find($id);
        }

        return view('Recrutamento.cadastro',$view);
    }

    public function getRecrutamento(){
        $IDOrg = Auth::user()->id_org;
        $SQL = <<<SQL
            SELECT 
                ps.Nome,
                DTInscricoes,
                ps.id as IDProcesso,
                COUNT(i.id) as QTInscricoes
            FROM processos_seletivos ps
            LEFT JOIN inscricoes i ON(i.IDProcesso = ps.id)
            WHERE ps.IDOrg = $IDOrg GROUP BY ps.id
        SQL;
        $rows = DB::select($SQL);
        //dd($rows);
        if(count($rows) > 0){
            foreach($rows as $r){
                $item = [];
                $item[] = $r->Nome;
                $item[] = $r->DTInscricoes;
                $item[] = $r->QTInscricoes;
                $item[] = "<a href=".route('Recrutamento/Edit',$r->IDProcesso)." class='btn btn-fr btn-xs'>Editar</a>";
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

}
