<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ProfessoresController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Ficha;
use App\Models\Resposta;
use App\Models\Escola;

class FichaController extends Controller
{
    public const submodulos = array([
        'nome' => 'Formulários',
        'rota' => 'Fichas/index',
        'endereco' => 'index'
    ]);

    public const cadastroSubmodulos = array([
        'nome' => 'Formulários',
        'rota' => 'Fichas/index',
        'endereco' => 'index'
    ],[
        'nome' => 'Respostas',
        'rota' => 'Fichas/Respostas',
        'endereco' => 'Respostas'
    ]);
    public function index(){
        return view('Fichas.index',[
            'submodulos' => self::submodulos,
            'id' => ''
        ]);
    }

    public function cadastro($id = null){
        $view = array(
            'id' => '',
            'submodulos' => self::submodulos,
            'Escolas' => ProfessoresController::getEscolasProfessor(Auth::user()->IDProfissional)
        );

        if($id){
            $view['id'] = $id;
            $view['Registro'] = Ficha::find($id);
            $view['submodulos'] = self::cadastroSubmodulos;
            $view['Ficha'] = json_decode($view['Registro']->Formulario);
        }

        return view('Fichas.cadastro', $view);
    }

    public function respostas($id){
        // Consulta SQL para obter registros de respostas
        // Consulta SQL para obter registros de respostas
        $registros = DB::select("
            SELECT r.Respostas, r.id, u.name 
            FROM respostas_ficha r 
            INNER JOIN ficha_avaliativa f ON (f.id = r.IDForm) 
            INNER JOIN users u ON (r.IDUser = u.id) 
            WHERE f.id = :id", ['id' => $id]);

        $respostaCount = [];

        if (count($registros) > 0) {
            foreach ($registros as $registro) {
                // Decodifica as respostas JSON para um array associativo
                $respostas = json_decode($registro->Respostas, true);

                // Conta o número de respostas para cada pergunta
                foreach ($respostas as $resposta) {
                    $pergunta = $resposta['Conteudo']; // Supondo que a pergunta esteja no JSON
                    $respostaTexto = isset($resposta['Resposta']) ? $resposta['Resposta'] : 'Sem Resposta';

                    // Incrementa a contagem de respostas por pergunta e tipo de resposta
                    if (!isset($respostaCount[$respostaTexto])) {
                        $respostaCount[$respostaTexto] = [];
                    }

                    if (!isset($respostaCount[$respostaTexto][$pergunta])) {
                        $respostaCount[$respostaTexto][$pergunta] = 0;
                    }

                    $respostaCount[$respostaTexto][$pergunta]++;
                }
            }
        }

        // Preparar os dados para passar para a view
        if(!$respostaCount){
            return false;
        }
        $labels = array_keys(reset($respostaCount)); // Usando as perguntas como labels
        $datasets = [];

        // Criar datasets para cada tipo de resposta
        foreach ($respostaCount as $resposta => $contagem) {
            $datasets[] = [
                'label' => $resposta, // Nome da resposta (ex: "Bom", "Ruim")
                'data' => array_values($contagem), // Valores de contagem de respostas
                'backgroundColor' => 'rgba(54, 162, 235, 0.6)', // Defina a cor conforme necessário
                'borderColor' => 'rgba(54, 162, 235, 1)',
                'borderWidth' => 1,
            ];
        }

        return view('Fichas.respostas',array(
            "submodulos" => self::cadastroSubmodulos,
            'labels' => $labels,
            'datasets' => $datasets,
            'respostas' => $respostas,
            "id" => $id
        ));
    }

    public function exportRespostas($id)
    {
        // Consulta os registros
        $registros = DB::select("
            SELECT r.Respostas, r.id, u.name 
            FROM respostas_ficha r 
            INNER JOIN ficha_avaliativa f ON (f.id = r.IDForm) 
            INNER JOIN users u ON (r.IDUser = u.id) 
            WHERE f.id = :id", ['id' => $id]);

        // Inicializa a planilha
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Define o cabeçalho
        $sheet->setCellValue('A1', 'Nome');
        $colIndex = 'B';

        if (count($registros) > 0) {
            $primeiroRegistro = json_decode($registros[0]->Respostas, true);
            foreach ($primeiroRegistro as $r) {
                $sheet->setCellValue($colIndex . '1', $r['Conteudo']);
                $colIndex++;
            }
        }

        // Preenche os dados
        $row = 2; // Começa na segunda linha (a primeira é o cabeçalho)
        foreach ($registros as $registro) {
            $item = [];
            $item[] = $registro->name;
            $respostas = json_decode($registro->Respostas, true);
            foreach ($respostas as $resposta) {
                $item[] = $resposta['Resposta'];
            }

            $colIndex = 'A';
            foreach ($item as $valor) {
                $sheet->setCellValue($colIndex . $row, $valor);
                $colIndex++;
            }
            $row++;
        }

        // Gera o arquivo Excel
        $fileName = 'relatorio_' . date('Y-m-d_H-i-s') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        // Cria a resposta para download
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function getRespostas($id){
        $registros = DB::select("
            SELECT r.Respostas, r.id, u.name 
            FROM respostas_ficha r 
            INNER JOIN ficha_avaliativa f ON (f.id = r.IDForm) 
            INNER JOIN users u ON (r.IDUser = u.id) 
            WHERE f.id = :id", ['id' => $id]);

            $itensJSON = [];

            if (count($registros) > 0) {
                foreach ($registros as $registro) {
                    $item = [];
                    // Adiciona o nome do usuário
                    $item[] = $registro->name;
                    
                    // Decodifica as respostas JSON para um array associativo
                    $respostas = json_decode($registro->Respostas, true);
                    
                    // Adiciona cada resposta ao array de itens
                    foreach ($respostas as $resposta) {
                        $item[] = $resposta['Resposta'];
                    }
                    
                    // Adiciona o item ao array de itens JSON
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

    public function visualizar($id){
        return view('Fichas.Ficha',array(
           "Ficha" => json_decode(Ficha::find($id)->Ficha),
           'id' => $id,
           'submodulos'=> self::submodulos
        ));
    }

    public function responder(Request $request){
        try{
            $respostas = $request->all();
            $Form = json_decode(Ficha::find($respostas['IDForm'])->Ficha,true);
            $respondidas = [];
            unset($respostas['_token']);
            unset($respostas['IDForm']);
            foreach($respostas as $rKey =>$rVal){
                $Form[$rKey]['Resposta'] = $rVal;
            }
            foreach($Form as $f){
                array_push($respondidas,$f);
            }
            $Respostas = json_encode($respondidas);
            Resposta::create(array(
                "Respostas" => $Respostas,
                "IDForm" => $request->IDForm,
                "IDUser" => Auth::user()->id
            ));
            $rota = 'Fichas/Visualizar';
            $mensagem = "Sua Resposta foi Enviada por Email";
            $aid = $request->IDForm;
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $aid = $request->IDForm;
            $rota = 'Fichas/Visualizar';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function save(Request $request){
        $data = $request->all();
        $arrayFormParsed = [];
        $arrayForm = array_map(function($a){
            if(!empty($a['Conteudo'])){
                return $a;
            }
        },json_decode($data['Ficha'],true));

        foreach($arrayForm as $af){
            if(!is_null($af)){
                array_push($arrayFormParsed,$af);
            }
        }
        $data['Formulario'] = json_encode($arrayFormParsed);
        try{
            if(!$request->id){
                Ficha::create($data);
            }else{
                Ficha::find($request->id)->update($data);
            }
            $situacao['mensagem'] = "Salvo";
            $situacao['status'] = 'success';
        }catch(\Throwable $th){
            $situacao['mensagem'] = 'Erro '. $th->getMessage();
            $situacao['status'] = 'success';
        }finally{
            return json_encode($situacao);
        }
    }

    public function getFichas(){
        if(Auth::user()->tipo == 6){
            $IDEscolas = ProfessoresController::getEscolasProfessor(Auth::user()->IDProfissional);
        }else{
            $IDEscolas = Escola::select('id')->where('IDOrg',Auth::user()->org_id)->toArray();
        }
        $registros = DB::select("SELECT f.Titulo,e.Titulo as Escola,f.id as IDForm FROM ficha_avaliativa f INNER JOIN escolas e ON(e.id = f.IDEscola) AND e.id IN($IDEscolas) ");
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Titulo;
                $item[] = $r->Escola;
                $item[] = "
                <a class='btn btn-success btn-xs' href=".route('Fichas/Edit',$r->IDForm).">Abrir</a>&nbsp
                <a class='btn btn-primary btn-xs' href=".route('Fichas/Visualizar',$r->IDForm).">Visualizar</a>&nbsp
                <a class='btn btn-secondary btn-xs' href=".route('Fichas/Respostas',$r->IDForm).">Respostas</a>
                ";
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
