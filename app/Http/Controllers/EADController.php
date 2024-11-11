<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\EAD;
use App\Models\EADInstituicao;
use App\Models\EADAnexo;
use App\Models\VEADInstituicao;
use App\Http\Controllers\EscolasController;
use App\Models\Escola;
use App\Models\EADEtapa;
use App\Models\VEADEtapa;
use App\Models\EADCurso;
use App\Models\VEADCurso;
use App\Models\EADAula;
use Illuminate\Http\Request;
use Storage;

class EADController extends Controller
{
    public const submodulos = array([
        "nome" => "EAD",
        "endereco" => "index",
        "rota" => "EAD/index"
    ],[
        "nome" => "Instituições",
        "endereco" => "Instituicoes",
        "rota" => "EAD/Instituicoes"
    ],[
        "nome" => "Cursos",
        "endereco" => "Cursos",
        "rota" => "EAD/Cursos"
    ]);

    public const cursoSubmodulos = array([
        "nome" => "Cursos",
        "endereco" => "Cursos",
        "rota" => "EAD/Cursos/Edit"
    ],[
        "nome" => "Aulas",
        "endereco" => "Aulas",
        "rota" => "EAD/Aulas"
    ],[
        "nome" => "Etapas",
        "endereco" => "Etapas",
        "rota" => "EAD/Etapas"
    ]);

    public function index(){
        return view("EAD.index",[
            "submodulos" => self::submodulos,
            "Regras" => EAD::where('IDOrg',Auth::user()->id_org)->first()
        ]);
    }

    public function instituicoes(){
        return view("EAD.instituicoes.index",[
            "submodulos" => self::submodulos
        ]);
    }

    public function cursos(){
        return view("EAD.cursos.index",[
            "submodulos" => self::submodulos
        ]);
    }

    public function etapas($IDCurso){
        return view('EAD.etapas.index',[
            "submodulos" => self::cursoSubmodulos,
            'id' => $IDCurso
        ]);
    }

    public function save(Request $request){
        try{
            if($request->id){
                $aid = $request->id;
                EADInstituicao::find($request->id)->update($request->all());
            }else{
                EADInstituicao::create($request->all());
                $aid = '';
            }

            $rout = "EAD/index";
            $mensagem = "Salvamento Feito com Sucesso";
            $status = 'success';
        }catch(\Thwrowable $th){
            $status = 'error';
            $rout = 'EAD/index';
            $mensagem = "Erro ao Salvar: ".$th->getMessage();
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function saveInstituicoes(Request $request){
        try{
            if($request->id){
                $aid = $request->id;
                EADInstituicao::find($request->id)->update($request->all());
                $rout = "EAD/Instituicoes/Edit";
            }else{
                EADInstituicao::create($request->all());
                $aid = '';
                $rout = "EAD/Instituicoes/Novo";
            }

            $mensagem = "Salvamento Feito com Sucesso";
            $status = 'success';
        }catch(\Thwrowable $th){
            $status = 'error';
            $rout = 'EAD/Instituicoes/Novo';
            $mensagem = "Erro ao Salvar: ".$th->getMessage();
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function saveAula(Request $request){
        //dd($request->file('Video'));
        try{
            if($request->id){
                $aid = ['IDCurso'=>$request->IDCurso,'id'=>$request->id];
                $rout = "EAD/Aulas/Edit";
                if($request->file('Video')){
                    $Video = $request->file('Video')->getClientOriginalName();
                    Storage::disk('public')->delete('organizacao_'.Auth::user()->id_org.'_escolas/escola_'. $request->IDEscola . '/Aula_'.$request->id."/". $request->oldVideo);
                    $request->file('Video')->storeAs('organizacao_'.Auth::user()->id_org.'_escolas/escola_'."/Aula_".$request->id."/".$request->IDEscola,$Video,'public');
                }else{
                    $Video = '';
                }
                //UPLOAD DE VIDEO
                EADAnexo::where('IDAula',$request->id)->update([
                    "IDAula" => $request->id,
                    "Anexo" => $Video,
                    "Tipo" => "Video"
                ]);
                //UPLOAD DE IMAGENS
                if($request->file('Imagens')){
                    foreach($request->file('Imagens') as $im){
                        if($im){
                            $Imagem = $request->file($im)->getClientOriginalName();
                            Storage::disk('public')->delete('organizacao_'.Auth::user()->id_org.'_escolas/escola_'. $request->IDEscola . '/Aula_'.$request->id."/". $im);
                            $im->storeAs('organizacao_'.Auth::user()->id_org.'_escolas/escola_'."/Aula_".$request->id."/".$request->IDEscola,$Imagem,'public');
                        }else{
                            $Imagem = '';
                        }

                        EADAnexo::where('IDAula',$request->id)->update([
                            "IDAula" => $request->id,
                            "Anexo" => $Imagem,
                            "Tipo" => "Imagem"
                        ]);
                    }
                }
                //UPLOAD DE ANEXOS
                if($request->file('PDF')){
                    foreach($request->file('PDF') as $pd){
                        if($pd){
                            $PDF = $request->file($pd)->getClientOriginalName();
                            Storage::disk('public')->delete('organizacao_'.Auth::user()->id_org.'_escolas/escola_'. $request->IDEscola . '/Aula_'.$request->id."/". $pd);
                            $pd->storeAs('organizacao_'.Auth::user()->id_org.'_escolas/escola_'."/Aula_".$request->id."/".$request->IDEscola,$Imagem,'public');
                        }else{
                            $PDF = '';
                        }

                        EADAnexo::where('IDAula',$request->id)->update([
                            "IDAula" => $request->id,
                            "Anexo" => $PDF,
                            "Tipo" => "PDF"
                        ]);
                    }
                }
                //
            }else{
                $aid = $request->IDCurso;
                $rout = "EAD/Aulas/Novo";
                $Aula = EADAula::create($request->all());
                if($request->file('Video')){
                    $Video = $request->file('Video')->getClientOriginalName();
                    $request->file('Video')->storeAs('organizacao_'.Auth::user()->id_org.'_escolas/escola_'.$request->IDEscola."/Aula_".$Aula->id,$Video,'public');
                }else{
                    $Video = '';
                }
                //UPLOAD DE VIDEO
                EADAnexo::create([
                    "IDAula" => $Aula->id,
                    "Anexo" => $Video,
                    "Tipo" => "Video"
                ]);
                //UPLOAD DE IMAGENS
                if($request->file('Imagens')){
                    foreach($request->file('Imagens') as $im){
                        $Imagem = $im->getClientOriginalName();
                        $im->storeAs('organizacao_'.Auth::user()->id_org.'_escolas/escola_'.$request->IDEscola."/Aula_".$Aula->id,$Imagem,'public');

                        EADAnexo::create([
                            "IDAula" => $Aula->id,
                            "Anexo" => $Imagem,
                            "Tipo" => "Imagem"
                        ]);
                    }
                }
                //UPLOAD DE ANEXOS
                if($request->file('PDF')){
                    foreach($request->file('PDF') as $pd){
                        $PDF = $pd->getClientOriginalName();
                        $pd->storeAs('organizacao_'.Auth::user()->id_org.'_escolas/escola_'.$request->IDEscola."/Aula_".$Aula->id,$Imagem,'public');

                        EADAnexo::create([
                            "IDAula" => $Aula->id,
                            "Anexo" => $PDF,
                            "Tipo" => "PDF"
                        ]);
                    }
                }
                //
            }

            $mensagem = "Salvamento Feito com Sucesso";
            $status = 'success';
        }catch(\Thwrowable $th){
            $aid = ['IDCurso'=>$request->IDCurso];
            $status = 'error';
            $rout = 'EAD/Aulas/Novo';
            $mensagem = "Erro ao Salvar: ".$th->getMessage();
        }finally{
            //dd($request->all());
            //return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function saveEtapa(Request $request){
        
        try{
            if($request->id){
                $aid = ['IDCurso'=>$request->IDCurso,'id'=>$request->id];
                EADEtapa::find($request->id)->update($request->all());
                $rout = "EAD/Etapas/Edit";
            }else{
                EADEtapa::create($request->all());
                $aid = $request->IDCurso;
                $rout = "EAD/Etapas/Novo";
            }

            $mensagem = "Salvamento Feito com Sucesso";
            $status = 'success';
        }catch(\Thwrowable $th){
            $status = 'error';
            $aid = $request->IDCurso;
            $rout = 'EAD/Etapas/Novo';
            $mensagem = "Erro ao Salvar: ".$th->getMessage();
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function saveCurso(Request $request){
        try{
            if($request->id){
                $aid = $request->id;
                EADCurso::find($request->id)->update($request->all());
                $rout = "EAD/Cursos/Edit";
            }else{
                EADCurso::create($request->all());
                $aid = '';
                $rout = "EAD/Cursos/Novo";
            }

            $mensagem = "Salvamento Feito com Sucesso";
            $status = 'success';
        }catch(\Thwrowable $th){
            $status = 'error';
            $rout = 'EAD/Cursos/Novo';
            $mensagem = "Erro ao Salvar: ".$th->getMessage();
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function cadastroInstituicoes($id=null){
        $view = array(
            "id" => '',
            "submodulos" => self::submodulos,
            "Escolas" => Escola::where('IDOrg',Auth::user()->id_org)->get()
        );

        if($id){
            $view['id'] = $id;
            $view['Registro'] = EADInstituicao::find($id);
        }

        return view('EAD.instituicoes.cadastro',$view);
    }

    public function cadastroCursos($id=null){
        $IDEscolas = EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional);
        
        $view = array(
            "id" => '',
            "submodulos" => self::submodulos,
            "Instituicoes" => EADInstituicao::whereIn('IDEscola',$IDEscolas)->get()
        );

        if($id){
            $view['id'] = $id;
            $view['submodulos'] = self::cursoSubmodulos;
            $view['Registro'] = EADCurso::find($id);
        }

        return view('EAD.cursos.cadastro',$view);
    }

    public function aulas($IDCurso){
        return view('EAD.aulas.index',[
            "submodulos" => self::cursoSubmodulos,
            'id' => $IDCurso
        ]);
    }

    public function cadastroEtapas($IDCurso,$id=null){
        $view = array(
            "id" => '',
            "IDCurso" => $IDCurso,
            "submodulos" => self::cursoSubmodulos
        );

        if($id){
            $view['id'] = $id;
            $view['Registro'] = EADEtapa::find($id);
        }

        return view('EAD.etapas.cadastro',$view);
    }

    public function cadastroAulas($IDCurso,$id=null){
        $view = array(
            "id" => '',
            "IDCurso" => $IDCurso,
            "submodulos" => self::cursoSubmodulos,
            "Etapas" => EADEtapa::where('IDCurso',$IDCurso)->get(),
            "IDEscola" => DB::select("SELECT i.IDEscola FROM ead_instituicoes i INNER JOIN ead_cursos c ON(c.IDInstituicao = i.id) WHERE c.id = $IDCurso")[0]->IDEscola
        );

        if($id){
            $view['id'] = $id;
            $view['Registro'] = EADAula::find($id);
        }

        return view('EAD.aulas.cadastro',$view);
    }

    public function getInstituicoes(){
        $IDOrg = Auth::user()->id_org;
        $SQL = "SELECT i.Nome as Instituicao,e.Nome as Escola,i.id as IDInstituicao FROM ead_instituicoes i INNER JOIN escolas e ON(e.id = i.IDEscola) WHERE e.IDOrg = $IDOrg";
        $registros = DB::select($SQL);

        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Instituicao;
                $item[] = $r->Escola;
                $item[] = "<a href='".route('EAD/Instituicoes/Edit',$r->IDInstituicao)."' class='btn btn-primary btn-xs'>Abrir</a>";
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

    public function getCursos(){
        $IDOrg = Auth::user()->id_org;
        $SQL = "SELECT c.NMCurso as Curso,i.Nome as Instituicao,c.id as IDCurso FROM ead_cursos c INNER JOIN ead_instituicoes i ON(i.id = c.id) INNER JOIN escolas e ON(i.IDEscola = e.id) WHERE e.IDOrg = $IDOrg";
        $registros = DB::select($SQL);

        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Curso;
                $item[] = $r->Instituicao;
                $item[] = "<a href='".route('EAD/Cursos/Edit',$r->IDCurso)."' class='btn btn-primary btn-xs'>Abrir</a>";
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

    public function getEtapas($IDCurso){
        
        $SQL = "SELECT et.id as IDEtapa,et.NMEtapa,et.DSEtapa FROM ead_etapas et INNER JOIN ead_cursos c ON(et.IDCurso = c.id) WHERE c.id = $IDCurso";
        $registros = DB::select($SQL);

        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->NMEtapa;
                $item[] = $r->DSEtapa;
                $item[] = "<a href='".route('EAD/Etapas/Edit',['id'=>$r->IDEtapa,'IDCurso'=>$IDCurso])."' class='btn btn-primary btn-xs'>Abrir</a>";
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

    public function getAulas($IDCurso){
        
        $SQL = "SELECT a.id as IDAula,a.NMAula,a.DSAula FROM ead_aulas a INNER JOIN ead_etapas et ON(et.id = a.IDEtapa) INNER JOIN ead_cursos c ON(et.IDCurso = c.ID) WHERE c.id = $IDCurso";
        $registros = DB::select($SQL);

        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->NMAula;
                $item[] = $r->DSAula;
                $item[] = "<a href='".route('EAD/Etapas/Edit',['id'=>$r->IDAula,'IDCurso'=>$IDCurso])."' class='btn btn-primary btn-xs'>Abrir</a>";
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
