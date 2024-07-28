<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aluno;
use App\Models\Matriculas;
use App\Models\Escola;
use App\Models\Turma;
use App\Models\FeedbackTransferencia;
use App\Models\Renovacoes;
use App\Models\Responsavel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Suspenso;
use App\Models\Situacao;
use App\Models\Transferencia;
use Storage;

class AlunosController extends Controller
{
    public const submodulos = array([
        "nome" => "Alunos",
        "endereco" => "index",
        "rota" => "Alunos/index"
    ],[
        "nome" => 'Transferidos',
        "endereco" => "Transferidos",
        "rota" => "Alunos/Transferidos"
    ]);

    public const professoresModulos = array([
        "nome" => "Alunos",
        "endereco" => "index",
        "rota" => "Alunos/index"
    ]);

    public const cadastroSubmodulos = array([
        "nome" => "Cadastro",
        "endereco" => "Edit",
        "rota" => "Alunos/Edit"
    ],[
        'nome' =>'Ficha',
        'endereco' => 'Ficha',
        'rota' => 'Alunos/Ficha'
    ],[
        'nome' => 'Atividades Desenvolvidas',
        'endereco' => 'Atividades',
        'rota' => 'Alunos/Atividades'
    ],[
        'nome' => 'Frequencia',
        'endereco' => 'Frequencia',
        'rota' => 'Alunos/Frequencia'
    ],[
        'nome' => 'Transferencias',
        'endereco' => 'Transferencias',
        'rota' => 'Alunos/Transferencias'
    ],[
        'nome' => 'Situação',
        'endereco' => 'Situacao',
        'rota' => 'Alunos/Situacao'
    ],[
        'nome' => 'Afastamento',
        'endereco' => 'Suspenso',
        'rota' => 'Alunos/Suspenso'
    ]);

    public const professoresSubmodulos = array([
        "nome" => "Cadastro",
        "endereco" => "Edit",
        "rota" => "Alunos/Edit"
    ],[
        'nome' => 'Atividades Desenvolvidas',
        'endereco' => 'Atividades',
        'rota' => 'Alunos/Atividades'
    ],[
        'nome' => 'Frequencia',
        'endereco' => 'Frequencia',
        'rota' => 'Alunos/Frequencia'
    ]);

    public function index(){

        if(self::getDados()['tipo'] == 6){
            $modulos = self::professoresModulos;
        }else{
            $modulos = self::submodulos;
        }

        return view('Alunos.index',[
            'submodulos' => $modulos,
            'Escolas' => Escola::where('IDOrg',Auth::user()->id_org)->get()
        ]);
    }

    public function suspenso($id){
        $idorg = Auth::user()->id_org;
        $SQL = "SELECT 
            a.id as IDAluno, 
            s.Justificativa,
            s.INISuspensao,
            s.TERSuspensao
        FROM matriculas m
        INNER JOIN alunos a ON(a.IDMatricula = m.id)
        INNER JOIN turmas t ON(a.IDTurma = t.id)
        LEFT JOIN suspensos s ON(s.IDInativo = a.id)
        INNER JOIN escolas e ON(t.IDEscola = e.id)
        INNER JOIN organizacoes o ON(e.IDOrg = o.id)
        INNER JOIN responsavel re ON(re.IDAluno = a.id)
        WHERE o.id = $idorg AND a.id = $id";

        return view('Alunos.suspensao',[
            'submodulos' => self::cadastroSubmodulos,
            'Registro' => DB::select($SQL)[0],
            'id'=> $id
        ]);
    }

    public function situacao($id){
        return view('Alunos.situacao',[
            'submodulos' => self::cadastroSubmodulos,
            'id' => $id
        ]);
    }

    public function cadastroSituacao($id){
        return view('Alunos.cadastroSituacao',[
            'submodulos'=>self::cadastroSubmodulos,
            'id' => $id
        ]);
    }

    public function saveSuspenso(Request $request){
        try{
            $suspenso = DB::select("SELECT id FROM suspensos WHERE IDInativo = $request->IDInativo AND INISuspensao < NOW() AND TERSuspensao > NOW()");
            $sAluno = $request->all();
            $sAluno['INISuspensao'] = date('Y-m-d');
            if($suspenso){
                Suspenso::where('IDInativo',$request->IDInativo)->update([
                    'INISuspensao' => $request->INISuspensao,
                    'TERSuspensao' => $request->TERSuspensao,
                    'Justificativa' => $request->Justificativa
                ]);
            }else{
                Suspenso::create([
                    'INISuspensao' => $request->INISuspensao,
                    'TERSuspensao' => $request->TERSuspensao,
                    'Justificativa' => $request->Justificativa,
                    'IDInativo' => $request->IDInativo
                ]);       
            }
            $rout = 'Alunos/Suspenso';
            $aid = $request->IDInativo;
            $status = 'success';
            $mensagem = 'Suspensão Realizada';
        }catch(\Throwable $th){
            $status = 'error';
            $mensagem = $th->getMessage();
            $rout = 'Alunos/Suspenso';
            $aid = $request->IDInativo;
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function removerSuspensao(Request $request){
        try{
            DB::delete("DELETE FROM suspensos WHERE IDInativo = $request->IDInativo");
        }catch(\Throwable $th){

        }finally{
            return redirect()->route('Alunos/Suspenso',$request->IDInativo)->with('success','Suspensão Removida! o Aluno Poderá Voltar as Aulas');
        }
    }

    public function ficha($id){
        $idorg = Auth::user()->id_org;
        $SQL = "SELECT 
            a.id as IDAluno, 
            m.id as IDMatricula,
            m.Nome as Nome,
            t.Nome as Turma,
            e.Nome as Escola,
            t.Serie as Serie,
            t.id as IDTurma,
            m.Nascimento as Nascimento,
            r.Vencimento as Vencimento,
            a.STAluno,
            m.Foto,
            m.Email,
            m.RG,
            m.CPF,
            re.NMResponsavel,
            re.RGPais,
            re.CPFResponsavel,
            re.EmailResponsavel,
            m.CEP,
            m.Rua,
            m.Bairro,
            m.UF,
            m.Numero,
            m.Cidade,
            re.CLResponsavel,
            a.IDTurma,
            m.Numero,
            m.Celular,
            m.NEE,
            m.Alergia,
            m.Transporte,
            m.BolsaFamilia,
            m.AMedico,
            m.APsicologico,
            m.CDPasta,
            m.AnexoRG,
            m.RGPaisAnexo,
            m.CResidencia,
            m.Historico
        FROM matriculas m
        INNER JOIN alunos a ON(a.IDMatricula = m.id)
        INNER JOIN turmas t ON(a.IDTurma = t.id)
        INNER JOIN renovacoes r ON(r.IDAluno = a.id)
        INNER JOIN escolas e ON(t.IDEscola = e.id)
        INNER JOIN organizacoes o ON(e.IDOrg = o.id)
        INNER JOIN responsavel re ON(re.IDAluno = a.id)
        WHERE o.id = $idorg AND a.id = $id  
        ";
        $Ficha = DB::select($SQL)[0];
        return view('Alunos.ficha',[
            'submodulos' => self::cadastroSubmodulos,
            'id' => $id,
            'Ficha' => $Ficha,
            'IDOrg' => Auth::user()->id_org,
            'Turmas' => Turma::where('IDEscola',$Ficha->IDTurma)->get()
        ]);
    }

    public function renovacoes($id){
        return view('Alunos.renovacoes',[
            'submodulos' => self::cadastroSubmodulos,
            'id' => $id,
            'IDEscola' => self::getEscolaDiretor(Auth::user()->id)
        ]);
    }

    public function frequencia($id){

        if(self::getDados()['tipo'] == 6){
            $submodulos = self::professoresSubmodulos;
        }else{
            $submodulos = self::cadastroSubmodulos;
        }

        return view('Alunos.frequencia',[
            'submodulos' => $submodulos,
            'id' => $id,
            'IDEscola' => self::getEscolaDiretor(Auth::user()->id)
        ]);
    }

    public function transferencias($id){

        if(self::getDados()['tipo'] == 6){
            $submodulos = self::professoresSubmodulos;
        }else{
            $submodulos = self::cadastroSubmodulos;
        }

        return view('Alunos.transferencias',[
            'submodulos' => $submodulos,
            'id' => $id
        ]);
    }

    public function faltasJustificadas($id){
        return view('Alunos.faltasJustificadas',[
            'submodulos' => self::cadastroSubmodulos,
            'id' => $id,
            'IDEscola' => self::getEscolaDiretor(Auth::user()->id)
        ]);
    }

    public function atividades($id){

        if(self::getDados()['tipo'] == 6){
            $submodulos = self::professoresSubmodulos;
        }else{
            $submodulos = self::cadastroSubmodulos;
        }

        return view('Alunos.atividades',[
            'submodulos' => $submodulos,
            'id' => $id,
            'IDEscola' => self::getEscolaDiretor(Auth::user()->id)
        ]);
    }

    public function matriculaTransferidos($id){

        $idorg = Auth::user()->id_org;
        $IDEscola = self::getEscolaDiretor(Auth::user()->id);
        $SQL = "SELECT
            a.id as IDAluno, 
            tr.id as IDTransferencia,
            eDestino.Nome as EscolaDestino,
            eOrigem.Nome as EscolaOrigem,
            tr.Justificativa,
            m.Foto,
            m.CDPasta,
            tr.created_at as DTTransferencia
        FROM transferencias tr
        INNER JOIN alunos a ON(a.id = tr.IDAluno)
        INNER JOIN matriculas m ON(a.IDMatricula = m.id)
        INNER JOIN turmas t ON(a.IDTurma = t.id)
        INNER JOIN escolas eDestino ON(tr.IDEscolaDestino = eDestino.id)
        INNER JOIN escolas eOrigem ON(tr.IDEscolaOrigem = eOrigem.id)
        INNER JOIN organizacoes o ON(eOrigem.IDOrg = o.id)
        WHERE o.id = $idorg AND eDestino.id = $IDEscola AND tr.id = $id    
        ";

        $view = [
            'submodulos' => self::submodulos,
            'id' => '',
            'Turmas' => Turma::where('IDEscola',self::getEscolaDiretor(Auth::user()->id))->get(),
            'Registro' => DB::select($SQL)[0]
        ];

        return view('Alunos.matriculaTransferidos',$view);
    }

    public function transferidos(){
        return view('Alunos.transferidos',[
            'submodulos' => self::submodulos,
            'id' => ''
        ]);
    }

    public function matricularTransferido(Request $request){
        try{
            if($request->IDTurma != 0){
                $IDEscola = self::getEscolaDiretor(Auth::user()->id);
                Aluno::find($request->IDAluno)->update(['IDTurma'=>$request->IDTurma]);
                Transferencia::find($request->IDTransferencia)->update(['Aprovado'=> 1]);
                DB::update("UPDATE escolas SET QTVagas = QTVagas-1 WHERE id = $IDEscola");
                $mensagem = 'Transferência Realizada com Sucesso!';
            }else{
                Transferencia::find($request->IDTransferencia)->update(['Aprovado'=> 2]);
                $mensagem = 'Transferência Reprovada';
            }

            FeedbackTransferencia::create(['IDTransferencia'=>$request->IDTransferencia,'Feedback'=>$request->Feedback]);
            $rout = 'Alunos/Transferidos';
            $aid = '';
            $status = 'success';
        }catch(\Throwable $th){
            $rout = 'Alunos/Transferidos/Transferido';
            $aid = $request->IDTransferencia;
            $status = 'error';
            $mensagem = 'Transferência Realizada com Sucesso!';
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function getTransferidos(){

        $idorg = Auth::user()->id_org;
        if(Auth::user()->tipo == 4){
            $IDEscola = self::getEscolaDiretor(Auth::user()->id);
            $AND = " AND eDestino.id=".$IDEscola;
        }else{
            $AND = "";
        }

        $SQL = "SELECT
            tr.id as IDTransferencia,
            a.id as IDAluno, 
            eDestino.Nome as EscolaDestino,
            eOrigem.Nome as EscolaOrigem,
            tr.Justificativa,
            tr.created_at as DTTransferencia
        FROM transferencias tr
        INNER JOIN alunos a ON(a.id = tr.IDAluno)
        INNER JOIN turmas t ON(a.IDTurma = t.id)
        INNER JOIN escolas eDestino ON(tr.IDEscolaDestino = eDestino.id)
        INNER JOIN escolas eOrigem ON(tr.IDEscolaOrigem = eOrigem.id)
        INNER JOIN organizacoes o ON(eOrigem.IDOrg = o.id)
        WHERE o.id = $idorg $AND AND tr.Aprovado = 0   
        ";

        $registros = DB::select($SQL);
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->EscolaOrigem;
                $item[] = Controller::data($r->DTTransferencia,'d/m/Y');
                $item[] = $r->Justificativa;
                $item[] = "<a href=".route('Alunos/Transferidos/Transferido',$r->IDTransferencia)." class='btn btn-fr btn-xs'>Aprovar/Reprovar</a>";
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

    public function cadastro($id=null){
        $idorg = Auth::user()->id_org;
        if(self::getDados()['tipo'] == 6){
            $submodulos = self::professoresModulos;
        }else{
            $submodulos = self::submodulos;
        }

        $view = [
            'submodulos' => $submodulos,
            'id' => '',
            'Turmas' => (Auth::user()->tipo == 4) ? Turma::where('IDEscola',self::getEscolaDiretor(Auth::user()->id))->get() : Turma::all()
        ];

        if($id){
            $SQL = "SELECT 
                a.id as IDAluno, 
                m.id as IDMatricula,
                m.Nome as Nome,
                t.Nome as Turma,
                e.Nome as Escola,
                t.Serie as Serie,
                m.Nascimento as Nascimento,
                r.Vencimento as Vencimento,
                a.STAluno,
                m.Foto,
                m.Email,
                m.RG,
                m.CPF,
                re.NMResponsavel,
                re.RGPais,
                re.CPFResponsavel,
                re.EmailResponsavel,
                m.CEP,
                m.Rua,
                m.Bairro,
                m.UF,
                m.Numero,
                m.Cidade,
                re.CLResponsavel,
                a.IDTurma,
                m.Numero,
                m.Celular,
                m.NEE,
                m.Alergia,
                m.Transporte,
                m.BolsaFamilia,
                m.AMedico,
                m.APsicologico,
                m.CDPasta,
                m.AnexoRG,
                re.RGPaisAnexo,
                m.CResidencia,
                m.Historico
            FROM matriculas m
            INNER JOIN alunos a ON(a.IDMatricula = m.id)
            INNER JOIN turmas t ON(a.IDTurma = t.id)
            INNER JOIN renovacoes r ON(r.IDAluno = a.id)
            INNER JOIN escolas e ON(t.IDEscola = e.id)
            INNER JOIN organizacoes o ON(e.IDOrg = o.id)
            INNER JOIN responsavel re ON(re.IDAluno = a.id)
            WHERE o.id = $idorg AND a.id = $id  
            ";

            $Registro = DB::select($SQL)[0];
            $Vencimento = Carbon::parse($Registro->Vencimento);
            $Hoje = Carbon::parse(date('Y-m-d'));
            if(self::getDados()['tipo'] == 6){
                $view['submodulos'] = self::professoresSubmodulos;
            }else{
                $view['submodulos'] = self::cadastroSubmodulos;
            }
   
            $view['id'] = $id;
            $view['IDOrg'] = Auth::user()->id_org;
            $view['Registro'] = $Registro;
            $view['Vencimento'] = $Vencimento;
            $view['Hoje'] = $Hoje;
        }

        return view('Alunos.cadastro',$view);
    }

    public function save(Request $request){
        try{
            $CDPasta = rand(0,99999999999);
            //dd($request->file('RGPaisAnexo')->getClientOriginalName());
            //
            if(!$request->IDMatricula || !$request->IDAluno){

                if($request->file('CResidencia')){
                    $CResidencia = $request->file('CResidencia')->getClientOriginalName();
                    $request->file('CResidencia')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$CDPasta,$CResidencia,'public');
                }else{
                    $CResidencia = '';
                }
    
                if($request->file('RGPaisAnexo')){
                    $RGPaisAnexo = $request->file('RGPaisAnexo')->getClientOriginalName();
                    $request->file('RGPaisAnexo')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$CDPasta,$RGPaisAnexo,'public');
                }else{
                    $RGPaisAnexo = '';
                }
    
                if($request->file('AnexoRG')){
                    $AnexoRG = $request->file('AnexoRG')->getClientOriginalName();
                    $request->file('AnexoRG')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$CDPasta,$AnexoRG,'public');
                }else{
                    $AnexoRG = '';
                }
    
                if($request->file('Historico')){
                    $Historico = $request->file('Historico')->getClientOriginalName();
                    $request->file('Historico')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$CDPasta,$Historico,'public');
                }else{
                    $Historico = '';
                }
    
                if($request->file('Foto')){
                    $Foto = $request->file('Foto')->getClientOriginalName();
                    $request->file('Foto')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$CDPasta,$Foto,'public');
                }else{
                    $Foto = '';
                }
    
                $matricula = array(
                    'AnexoRG' => $AnexoRG,
                    'CResidencia' => $CResidencia,
                    'Historico' => $Historico,
                    'Nome' => $request->Nome,
                    'CPF' => preg_replace('/\D/', '', $request->CPF),
                    'RG' => preg_replace('/\D/', '', $request->RG),
                    'CEP' => preg_replace('/\D/', '', $request->CEP),
                    'Rua' => $request->Rua,
                    'Email' => $request->Email,
                    'Celular' => preg_replace('/\D/', '', $request->Celular),
                    'UF' => $request->UF,
                    'Cidade' => $request->Cidade,
                    'BolsaFamilia' => $request->BolsaFamilia,
                    'Alergia' => $request->Alergia,
                    'Transporte' => $request->Transporte,
                    'NEE' => $request->NEE,
                    'AMedico' => $request->AMedico,
                    'APsicologico' => $request->APsicologico,
                    'Aprovado' => 1,
                    'Foto' => $Foto,
                    'RGPaisAnexo' => $RGPaisAnexo,
                    'Bairro' => $request->Bairro,
                    'Numero' => $request->Numero,
                    'Nascimento' => $request->Nascimento,
                    'CDPasta' => $CDPasta
                );

                $createMatricula = Matriculas::create($matricula);

                $aluno = array(
                    'IDMatricula' => $createMatricula->id,
                    'STAluno' => 0,
                    'IDTurma' => $request->IDTurma
                );

                $createAluno = Aluno::create($aluno);

                $renovacao = array(
                    'IDAluno' => $createAluno->id,
                    'Aprovado' => 1,
                    'Vencimento' => $request->Vencimento,
                    'ANO' => date('Y')
                );

                Renovacoes::create($renovacao);

                $responsavel = array(
                    'IDAluno' => $createAluno->id,
                    'RGPaisAnexo' => $request->RGPaisAnexo,
                    'RGPais' => preg_replace('/\D/', '', $request->RGPais),
                    'NMResponsavel' => $request->NMResponsavel,
                    'EmailResponsavel' => $request->EmailResponsavel,
                    'CLResponsavel' => preg_replace('/\D/', '', $request->CLResponsavel),
                    'CPFResponsavel' => preg_replace('/\D/', '', $request->CPFResponsavel)
                );

                Responsavel::create($responsavel);
                $IDEscola = self::getEscolaDiretor(Auth::user()->id);
                DB::update("UPDATE escolas SET QTVagas = QTVagas-1 WHERE id = $IDEscola");
                $aid = '';
                $rout = 'Alunos/Novo';
            }else{
                
                if($request->file('CResidencia')){
                    $CResidencia = $request->file('CResidencia')->getClientOriginalName();
                    Storage::disk('public')->delete('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'. $request->CDPasta . '/' . $request->oldCResidencia);
                    $request->file('CResidencia')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$request->CDPasta,$CResidencia,'public');
                }else{
                    $CResidencia = '';
                }
    
                if($request->file('RGPaisAnexo')){
                    $RGPaisAnexo = $request->file('RGPaisAnexo')->getClientOriginalName();
                    Storage::disk('public')->delete('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'. $request->CDPasta . '/' . $request->oldRGPaisAnexo);
                    $request->file('RGPaisAnexo')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$request->CDPasta,$RGPaisAnexo,'public');
                }else{
                    $RGPaisAnexo = '';
                }
    
                if($request->file('AnexoRG')){
                    $AnexoRG = $request->file('AnexoRG')->getClientOriginalName();
                    Storage::disk('public')->delete('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'. $request->CDPasta . '/' . $request->oldAnexoRG);
                    $request->file('AnexoRG')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$request->CDPasta,$AnexoRG,'public');
                }else{
                    $AnexoRG = '';
                }
    
                if($request->file('Historico')){
                    $Historico = $request->file('Historico')->getClientOriginalName();
                    Storage::disk('public')->delete('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'. $request->CDPasta . '/' . $request->oldHistorico);
                    $request->file('Historico')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$request->CDPasta,$Historico,'public');
                }else{
                    $Historico = '';
                }
    
                if($request->file('Foto')){
                    $Foto = $request->file('Foto')->getClientOriginalName();
                    Storage::disk('public')->delete('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'. $request->CDPasta . '/' . $request->oldFoto);
                    $request->file('Foto')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$request->CDPasta,$Foto,'public');
                }else{
                    $Foto = '';
                }
    
                $matricula = array(
                    'AnexoRG' => $AnexoRG,
                    'CResidencia' => $CResidencia,
                    'Historico' => $Historico,
                    'Nome' => $request->Nome,
                    'CPF' => preg_replace('/\D/', '', $request->CPF),
                    'RG' => preg_replace('/\D/', '', $request->RG),
                    'CEP' => preg_replace('/\D/', '', $request->CEP),
                    'Rua' => $request->Rua,
                    'Email' => $request->Email,
                    'Celular' => preg_replace('/\D/', '', $request->Celular),
                    'UF' => $request->UF,
                    'Cidade' => $request->Cidade,
                    'BolsaFamilia' => $request->BolsaFamilia,
                    'Alergia' => $request->Alergia,
                    'Transporte' => $request->Transporte,
                    'NEE' => $request->NEE,
                    'AMedico' => $request->AMedico,
                    'APsicologico' => $request->APsicologico,
                    'Aprovado' => 1,
                    'RGPaisAnexo' => $RGPaisAnexo,
                    'Foto' => $Foto,
                    'Bairro' => $request->Bairro,
                    'Numero' => $request->Numero,
                    'Nascimento' => $request->Nascimento
                );

                if(empty($Historico)){
                    unset($matricula['Historico']);
                }

                if(empty($AnexoRG)){
                    unset($matricula['AnexoRG']);
                }

                if(empty($CResidencia)){
                    unset($matricula['CResidencia']);
                }

                if(empty($Foto)){
                    unset($matricula['Foto']);
                }

                if(empty($request->RG)){
                    unset($matricula['RG']);
                }

                if(empty($request->CPF)){
                    unset($matricula['CPF']);
                }

                if(empty($RGPaisAnexo)){
                    unset($matricula['RGPaisAnexo']);
                }

                if(is_null($request->Nome)){
                    unset($matricula['Nome']);    
                }

                if(is_null($request->Nascimento)){
                    unset($matricula['Nascimento']);    
                }

                //dd($matricula);

                Matriculas::find($request->IDMatricula)->update($matricula);

                $aluno = array(
                    'STAluno' => 0,
                    'IDTurma' => $request->IDTurma
                );

                Aluno::find($request->IDAluno)->update($aluno);

                $renovacao = array(
                    'Aprovado' => 1,
                    'Vencimento' => $request->Vencimento,
                    'ANO' => date('Y')
                );

                Renovacoes::where('IDAluno',$request->IDAluno)->update($renovacao);

                $responsavel = array(
                    'RGPaisAnexo' => $request->RGPaisAnexo,
                    'RGPais' => preg_replace('/\D/', '', $request->RGPais),
                    'NMResponsavel' => $request->NMResponsavel,
                    'EmailResponsavel' => $request->EmailResponsavel,
                    'CLResponsavel' => preg_replace('/\D/', '', $request->CLResponsavel),
                    'CPFResponsavel' => preg_replace('/\D/', '', $request->CPFResponsavel)
                );

                Responsavel::where('IDAluno',$request->IDAluno)->update($responsavel);

                $aid = $request->IDAluno;
                $rout = 'Alunos/Edit';
            }
            $status = 'success';
            $mensagem = 'Salvamento Feito com Sucesso!';
        }catch(\Throwable $th){
            $status = 'error';
            $mensagem = $th->getMessage();
            $rout = 'Alunos/Novo';
            $aid = '';
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function renovar(Request $request){
        try{
            DB::update("UPDATE renovacoes SET ANO = ANO + 1,Vencimento = DATE_ADD(Vencimento,INTERVAL 1 YEAR) WHERE IDAluno = '$request->IDAluno'");
            $rout = 'Alunos/Edit';
            $aid = $request->IDAluno;
            $status = 'success';
            $mensagem = 'Matrícula Renovada com Sucesso!';
        }catch(\Throwable $th){
            $status = 'error';
            $mensagem = $th->getMessage();
            $rout = 'Alunos/Novo';
            $aid = '';
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function saveSituacao(Request $request){
        try{
            if($request->STAluno > 0){
                DB::update('UPDATE escolas SET QTVagas = QTVagas + 1');
            }else{
                DB::update('UPDATE escolas SET QTVagas = QTVagas - 1');
            }
            Situacao::create([
                'Justificativa' => $request->Justificativa,
                'IDAluno' => $request->IDAluno,
                'STAluno' => $request->STAluno
            ]);

            Aluno::where('id',$request->IDAluno)->update(['STAluno'=> $request->STAluno]);

            $status = 'success';
            $mensagem = "Situação Atualizada com Sucesso!";
            $rout = 'Alunos/Situacao/Novo';
            $aid = $request->IDAluno;
        }catch(\Throwable $th){
            $status = 'error';
            $mensagem = $th->getMessage();
            $rout = 'Alunos/Situacao/Novo';
            $aid = $request->IDAluno;
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function cancelaTransferencia(Request $request){
        try{
            Transferencia::find($request->IDTransferencia)->delete();
            $status = 'success';
            $mensagem = "Transferência Cancelada com Sucesso!";
            $rout = 'Alunos/Transferencias';
            $aid = $request->IDAluno;
        }catch(\Throwable $th){
            $status = 'error';
            $mensagem = $th->getMessage();
            $rout = 'Alunos/Transferencias';
            $aid = $request->IDAluno;
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function saveTransferencias(Request $request){
        try{
            if($request->IDEscolaOrigem !=0){
                Transferencia::create($request->all());
            }else{
                $trs = $request->all();
                $trs['Aprovado'] = 3;
                Transferencia::create($trs);
            }
            $status = 'success';
            $mensagem = "Transferência Feita com Sucesso!";
            $rout = 'Alunos/Transferencias';
            $aid = $request->IDAluno;
        }catch(\Throwable $th){
            $status = 'error';
            $mensagem = $th->getMessage();
            $rout = 'Alunos/Transferencias';
            $aid = $request->IDAluno;
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function getTransferencias($IDAluno){
        $idorg = Auth::user()->id_org;
        $SQL = "SELECT
            a.id as IDAluno, 
            tr.id as IDTransferencia,
            eOrigem.Nome as EscolaOrigem,
            tr.Justificativa,
            tr.Aprovado,
            CASE WHEN tr.IDEscolaDestino = 0 THEN 'Escola Fora da Rede' ELSE eDestino.Nome END as Destino,
            t.Nome as Turma,
            tr.created_at as DTTransferencia,
            CASE WHEN ft.Feedback IS NOT NULL THEN ft.Feedback ELSE '' END as Feedback
        FROM transferencias tr
        INNER JOIN alunos a ON(a.id = tr.IDAluno)
        INNER JOIN turmas t ON(a.IDTurma = t.id)
        INNER JOIN escolas eOrigem ON(tr.IDEscolaOrigem = eOrigem.id)
        LEFT JOIN escolas eDestino ON(tr.IDEscolaDestino = eDestino.id)
        LEFT JOIN feedback_transferencias as ft ON(ft.IDTransferencia = tr.id)
        INNER JOIN organizacoes o ON(eOrigem.IDOrg = o.id)
        WHERE o.id = $idorg AND a.id = $IDAluno   
        ";

        $registros = DB::select($SQL);
        if(count($registros) > 0){
            foreach($registros as $r){
                switch($r->Aprovado){
                    case '0':
                        $st = "<strong class='text-warning'>Pendente</strong>";
                    break;
                    case '1':
                        $st = "<strong class='text-success'>Aprovado</strong>";
                    break;
                    case '2':
                        $st = "<strong class='text-danger'>Reprovado (".$r->Feedback.")</strong>";
                    break;
                    case '3':
                        $st = "<strong class='text-secondary'>Transferido Para Fora da Rede</strong>";
                    break;
                }
                $item = [];
                $item[] = $r->EscolaOrigem;
                $item[] = $r->Destino;
                $item[] = Controller::data($r->DTTransferencia,'d/m/Y');
                $item[] = $r->Justificativa;
                $item[] = $st;
                $item[] = "<form id='formCancelaTransferencia' style='display:none' method='POST' action=".route('Alunos/Transferencias/Cancela').">
                    <input type='hidden' name='_token' value=".csrf_token().">
                    <input type='hidden' name='IDAluno' value='$r->IDAluno'>
                    <input type='hidden' name='IDTransferencia' value='$r->IDTransferencia'>
                </form>
                <button class='btn btn-xs btn-danger' onclick='cancelarTransferencia($r->IDTransferencia)'>Cancelar Transferencia</button>";
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

    function cadastroTransferencias($IDAluno){
        return view('Alunos.cadastroTransferencia',[
            'submodulos'=>self::cadastroSubmodulos,
            'id' => $IDAluno,
            'IDEscola' => self::getEscolaDiretor(Auth::user()->id),
            'Escolas' => Escola::where('IDOrg',Auth::user()->id_org)->get()
        ]);
    }

    public function getAlunos(){
        $idorg = Auth::user()->id_org;

        if(Auth::user()->tipo == 4){
            $IDEscola = self::getEscolaDiretor(Auth::user()->id);
            $AND = ' AND e.id='.$IDEscola;
            //dd($AND);
        }else{
            $AND = '';
        }

        if(Auth::user()->tipo == 6){
            $AND .= " AND t.IDEscola IN(".implode(',',self::getCurrentEscolasProfessor(Auth::user()->id)).")";
        }else{
            $AND .=' ';
        }

        if(isset($_GET['Status']) && !empty($_GET['Status'])){
            $AND .= " AND a.STAluno=".$_GET['Status'];
        }

        if(isset($_GET['Escola']) && !empty($_GET['Escola'])){
            $AND .= " AND e.id=".$_GET['Escola'];
        }

        $SQL = "SELECT
            a.id as IDAluno, 
            m.Nome as Nome,
            t.Nome as Turma,
            e.Nome as Escola,
            t.Serie as Serie,
            m.Nascimento as Nascimento,
            MAX(r.Vencimento) as Vencimento,
            a.STAluno,
            m.Foto,
            m.Email,
            MAX(tr.Aprovado) as Aprovado
        FROM matriculas m
        INNER JOIN alunos a ON(a.IDMatricula = m.id)
        LEFT JOIN transferencias tr ON(tr.IDAluno = a.id)
        INNER JOIN turmas t ON(a.IDTurma = t.id)
        INNER JOIN renovacoes r ON(r.IDAluno = a.id)
        INNER JOIN escolas e ON(t.IDEscola = e.id)
        INNER JOIN organizacoes o ON(e.IDOrg = o.id)
        WHERE o.id = $idorg $AND GROUP BY a.id    
        ";
        //dd($SQL);
        $registros = DB::select($SQL);
        if(count($registros) > 0){
            foreach($registros as $r){
                switch($r->STAluno){
                    case "0":
                        $Situacao = 'Frequente';
                    break;
                    case "1":
                        $Situacao = "Evadido";
                    break;
                    case "2":
                        $Situacao = "Desistente";
                    break;
                    case "3":
                        $Situacao = "Desligado";
                    break;
                    case "4":
                        $Situacao = "Egresso";
                    break;
                    case "5":
                        $Situacao = "Transferido Para Outra Rede";
                    break;
                }

                if($r->Aprovado == 3){
                    $transferido = "<strong class='text-danger'>(Aluno Transferido Para Outra Rede)</strong>";
                }else{
                    $transferido = '';
                }

                $Vencimento = Carbon::parse($r->Vencimento);
                $Hoje = Carbon::parse(date('Y-m-d'));

                $item = [];
                $item[] = $r->Nome." ".$transferido;
                $item[] = $r->Turma;
                (Auth::user()->tipo == 2) ? $item[] = $r->Escola : '';
                $item[] = $r->Serie;
                $item[] = Controller::data($r->Nascimento,'d/m/Y');
                $item[] = Controller::data($r->Vencimento,'d/m/Y');
                $item[] = $Vencimento->lt($Hoje) ? "<strong class='text-danger'>PENDENTE RENOVAÇÃO</strong>" : "<strong class='text-success'>EM DIA</strong>";
                $item[] = $Situacao;
                $item[] = " <a href='".route('Alunos/Edit',$r->IDAluno)."' class='btn btn-primary btn-xs'>Visualizar</a>";
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

    public function getSituacao($id){
        $idorg = Auth::user()->id_org;

        if(Auth::user()->tipo == 4){
            $IDEscola = self::getEscolaDiretor(Auth::user()->id);
            $AND = ' AND e.id='.$IDEscola;
            //dd($AND);
        }else{
            $AND = '';
        }

        $SQL = "SELECT
            a.id as IDAluno, 
            at.STAluno,
            a.STAluno as alunoST,
            at.Justificativa,
            at.created_at
        FROM matriculas m
        INNER JOIN alunos a ON(a.IDMatricula = m.id)
        INNER JOIN turmas t ON(t.id = a.IDTurma)
        INNER JOIN alteracoes_situacao at ON(at.IDAluno = a.id)
        INNER JOIN escolas e ON(t.IDEscola = e.id)
        INNER JOIN organizacoes o ON(e.IDOrg = o.id)
        WHERE o.id = $idorg AND a.id = $id $AND    
        ";

        $registros = DB::select($SQL);
        if(count($registros) > 0){
            foreach($registros as $r){
                switch($r->STAluno){
                    case "0":
                        $Situacao = 'Frequente';
                    break;
                    case "1":
                        $Situacao = "Evadido";
                    break;
                    case "2":
                        $Situacao = "Desistente";
                    break;
                    case "3":
                        $Situacao = "Desligado";
                    break;
                    case "4":
                        $Situacao = "Egresso";
                    break;
                    case "5":
                        $Situacao = "Transferido Para Outra Rede";
                    break;
                }

                $item = [];
                $item[] = $Situacao;
                $item[] = Controller::data($r->created_at,'d/m/Y');
                $item[] = $r->Justificativa;
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
}
