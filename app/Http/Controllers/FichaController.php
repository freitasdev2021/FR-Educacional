<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ProfessoresController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Ficha;
use App\Models\Resposta;
use Codedge\Fpdf\Fpdf\Fpdf;
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
            'Escolas' => ProfessoresController::getDadosEscolasProfessor(Auth::user()->IDProfissional)
        );

        if($id){
            $rsp = Resposta::all()->where('IDForm',$id)->first();
            $view['id'] = $id;
            $view['Registro'] = Ficha::find($id);
            $view['submodulos'] = self::cadastroSubmodulos;
            $view['Formulario'] = json_decode($rsp);
        }

        return view('Fichas.cadastro', $view);
    }

    public function respostas($id){
        // Consulta SQL para obter registros de respostas
        // Consulta SQL para obter registros de respostas
        $Registro = DB::select("SELECT r.Respostas FROM respostas_ficha r WHERE r.IDFicha = $id")[0];

        return view('Fichas.respostas',array(
            "submodulos" => self::cadastroSubmodulos,
            "respostas" => json_decode($Registro->Respostas),
            "id" => $id
        ));
    }

    public function exportRespostas($id)
    {
        // Consulta os registros
        $registros = DB::select("
        SELECT r.Respostas, r.id, m.Nome 
        FROM respostas_ficha r 
        INNER JOIN ficha_avaliativa f ON (f.id = r.IDFicha) 
        INNER JOIN alunos a ON (r.IDAluno = a.id)
        INNER JOIN matriculas m ON(m.id = a.IDMatricula) 
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
            $item[] = $registro->Nome;
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

    public function exportarRespostasPDF($id)
    {
        // Consulta os dados dos registros
        $registros = DB::select("
            SELECT r.Respostas, r.id, m.Nome 
            FROM respostas_ficha r 
            INNER JOIN ficha_avaliativa f ON (f.id = r.IDFicha) 
            INNER JOIN alunos a ON (r.IDAluno = a.id)
            INNER JOIN matriculas m ON(m.id = a.IDMatricula) 
            WHERE f.id = :id", ['id' => $id]);

        // Inicializa o PDF
        $pdf = new Fpdf();
        $pdf->SetMargins(20, 20, 20); // Margens de 20 em todos os lados

        // Obtém informações da escola
        $IDAluno = Resposta::where('IDFicha', $id)->first()->IDAluno;
        $Escola = DB::select("
            SELECT e.id, e.Cidade, e.UF, e.Foto, m.Nome as Aluno, e.Foto, e.Nome as Escola 
            FROM escolas e 
            INNER JOIN turmas t ON(t.IDEscola = e.id) 
            INNER JOIN alunos a ON(t.id = a.IDTurma ) 
            INNER JOIN matriculas m ON(m.id = a.IDMatricula) 
            WHERE a.id = :IDAluno", ['IDAluno' => $IDAluno])[0];

        // Cabeçalho com a logo e nome da escola (uma vez)
        $pdf->AddPage();
        $pdf->Image(public_path('storage/organizacao_' . Auth::user()->id_org . '_escolas/escola_' . $Escola->id . '/' . $Escola->Foto), 10, 10, 30); // Logo da escola
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetXY(50, 15);
        $pdf->Cell(0, 10, self::utfConvert($Escola->Escola), 0, 1, 'C'); // Nome da escola
        $pdf->Ln(30);

        $boletinsPorPagina = 0;

        foreach ($registros as $registro) {
            // Adiciona nova página a cada dois boletins
            if ($boletinsPorPagina % 2 == 0 && $boletinsPorPagina > 0) {
                $pdf->AddPage();
            }

            // Exibe o nome do aluno em uma célula destacada
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 10, 'Aluno: ' . self::utfConvert($registro->Nome), 1, 1, 'L');
            $pdf->Ln(5);

            // Cabeçalho da tabela para cada aluno
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(140, 8, 'Conteudo', 1, 0, 'C');
            $pdf->Cell(30, 8, 'Resposta', 1, 1, 'C');

            // Adiciona as respostas
            $pdf->SetFont('Arial', '', 10);
            $respostas = json_decode($registro->Respostas, true);
            foreach ($respostas as $resposta) {
                $pdf->Cell(140, 8, self::utfConvert($resposta['Conteudo']), 1, 0);
                $pdf->Cell(30, 8, self::utfConvert($resposta['Resposta']), 1, 1);
            }

            $pdf->Ln(10); // Espaço entre os boletins
            $boletinsPorPagina++;
        }

        // Saída do PDF
        $pdf->Output();
        exit;
    }


    public function getRespostas($id){
        $registros = DB::select("
            SELECT r.Respostas, r.id, m.Nome 
            FROM respostas_ficha r 
            INNER JOIN ficha_avaliativa f ON (f.id = r.IDFicha) 
            INNER JOIN alunos a ON (r.IDAluno = a.id)
            INNER JOIN matriculas m ON(m.id = a.IDMatricula) 
            WHERE f.id = :id", ['id' => $id]);

            $itensJSON = [];

            if (count($registros) > 0) {
                foreach ($registros as $registro) {
                    $item = [];
                    // Adiciona o nome do usuário
                    $item[] = $registro->Nome;
                    
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
        return view('Fichas.formulario',array(
           "Ficha" => json_decode(Ficha::find($id)->Formulario),
           'id' => $id,
           "Alunos" => ProfessoresController::getAlunosProfessor(Auth::user()->IDProfissional),
           'submodulos'=> self::submodulos
        ));
    }

    public function responder(Request $request){
        try{
            $respostas = $request->all();
            $Form = json_decode(Ficha::find($respostas['IDFicha'])->Formulario,true);
            
            $respondidas = [];
            unset($respostas['_token']);
            unset($respostas['IDFicha']);
            foreach($respostas as $rKey =>$rVal){
                $Form[$rKey]['Resposta'] = $rVal;
            }
            
            foreach($Form as $f){
                if(!empty($f['Conteudo'])){
                    array_push($respondidas,$f);
                }
            }
            
            $Rsp = array_map(function($ar){
                if(!is_null($ar)){
                    return $ar;
                }
            },$respondidas);
            
            Resposta::create(array(
                "Respostas" => json_encode($Rsp),
                "IDFicha" => $request->IDFicha,
                "IDAluno" => $request->IDAluno
            ));
            $rota = 'Fichas/Visualizar';
            $mensagem = "Ficha Respondida!";
            $aid = $request->IDFicha;
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $aid = $request->IDFicha;
            $rota = 'Fichas/Visualizar';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function save(Request $request){
        try{
            $data = $request->all();
            $arrayFormParsed = [];
            
            $arrayForm = array_map(function($a){
                if(!empty($a['Conteudo'])){
                    return $a;
                }
            },json_decode($data['Formulario'],true));
            
            foreach($arrayForm as $af){
                if(!is_null($af)){
                    array_push($arrayFormParsed,$af);
                }
            }
            
            $data['Ficha'] = json_encode($arrayFormParsed);
            
            if(!$request->id){
                //MailController::send($request->email,'Confirmação - Organizador','Mail.cadastroorganizador',array('Senha'=> $RandPW,'Email'=> $request->email));
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
        $IDEscolas = implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional));

        $registros = DB::select("SELECT f.Titulo,e.Nome as Escola,f.id as IDFicha FROM ficha_avaliativa f INNER JOIN escolas e ON(e.id = f.IDEscola) AND e.id IN($IDEscolas) ");
        
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Titulo;
                $item[] = $r->Escola;
                $item[] = "
                <a class='btn btn-danger btn-xs' href=".route('Fichas/Respostas/Export',$r->IDFicha).">Exportar Respostas</a>&nbsp
                <a class='btn btn-danger btn-xs' href=".route('Fichas/Respostas/Export/PDF',$r->IDFicha).">Exportar PDF</a>&nbsp
                <a class='btn btn-success btn-xs' href=".route('Fichas/Edit',$r->IDFicha).">Abrir</a>&nbsp
                <a class='btn btn-primary btn-xs' href=".route('Fichas/Visualizar',$r->IDFicha).">Visualizar</a>&nbsp
                <a class='btn btn-secondary btn-xs' href=".route('Fichas/Respostas',$r->IDFicha).">Respostas</a>
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
