<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Livro;
use App\Models\Leitor;
use App\Models\Emprestimo;
use App\Models\BARCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Carbon\Carbon;
use Codedge\Fpdf\Fpdf\Fpdf;

class BibliotecaController extends Controller
{
    public const submodulos = array([
        "nome" => "Cadastro",
        "endereco" => "index",
        "rota"=> "Biblioteca/index"
    ],[
        "nome" => "Emprestimos",
        "endereco" => "Emprestimos",
        "rota" => "Biblioteca/Emprestimos/index"
    ],[
        "nome" => "Leitores",
        "endereco" => "Leitores",
        "rota" => "Biblioteca/Leitores/index"
    ]);

    public function index(){
        return view('Biblioteca.index',[
            "submodulos" => self::submodulos
        ]);
    }

    public function emprestimos(){
        return view('Biblioteca.emprestimos',[
            "submodulos" => self::submodulos
        ]);
    }

    public function leitores(){
        return view('Biblioteca.leitores',[
            "submodulos" => self::submodulos
        ]);
    }

    public function save(Request $request){
        try{
            if($request->id){
                Livro::find($request->id)->update($request->all());
                $aid = $request->id;
                $rota = 'Biblioteca/Edit';
            }else{
                Livro::create($request->all());
                $aid = '';
                $rota = 'Biblioteca/Novo';
            }
            $mensagem = "Salvamento feito com Sucesso";
            $status = 'success';
        }catch(\Throwable $th){
            $rota = 'Biblioteca/Novo';
            $mensagem = 'Erro '.$th->getMessage();
            $aid = '';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function saveEmprestimos(Request $request){
        try{
            if($request->id){
                Emprestimo::find($request->id)->update($request->all());
                $aid = $request->id;
                $rota = 'Biblioteca/Emprestimos/Edit';
            }else{
                Emprestimo::create($request->all());
                $aid = '';
                $rota = 'Biblioteca/Emprestimos/Novo';
            }

            $mensagem = "Salvamento feito com Sucesso";
            $status = 'success';
        }catch(\Throwable $th){
            $rota = 'Biblioteca/Emprestimos/Novo';
            $mensagem = 'Erro '.$th;
            $aid = '';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function saveLeitores(Request $request){
        try{
            $data = $request->all();
            $data['EnderecoJSON'] = json_encode([
                "Rua" => $request->Rua,
                "Numero" => $request->Numero,
                "Cidade" => $request->Cidade,
                "Bairro" => $request->Bairro,
                "CEP" => $request->CEP,
                "UF" => $request->UF
            ]);
            $data['IDOrg'] = Auth::user()->id_org;
            if($request->id){
                Leitor::find($request->id)->update($data);
                $aid = $request->id;
                $rota = 'Biblioteca/Leitores/Edit';
            }else{
                Leitor::create($data);
                $aid = '';
                $rota = 'Biblioteca/Leitores/Novo';
            }

            $mensagem = "Salvamento feito com Sucesso";
            $status = 'success';
        }catch(\Throwable $th){
            $rota = 'Biblioteca/Leitores/Novo';
            $mensagem = 'Erro '.$th;
            $aid = '';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function getBibliotecas(){
        $IDOrg = Auth::user()->id_org;
        $SQL = <<<SQL
        SELECT
            l.Nome as Livro,
            l.Autor,
            l.Classificacao,
            l.Editora,
            l.id,
            cl.Codigo
        FROM livros l 
        INNER JOIN cod_livros cl ON(cl.id = l.IDCodigo)
        WHERE cl.IDOrg = $IDOrg
        SQL;

        $rows = DB::select($SQL);
        if(count($rows) > 0){
            foreach($rows as $r){
                $item = [];
                $item[] = $r->Codigo;
                $item[] = $r->Livro;
                $item[] = $r->Autor;
                $item[] = $r->Classificacao;
                $item[] = $r->Editora;
                $item[] = "<a href='".route('Biblioteca/Edit',$r->id)."' class='btn btn-primary btn-xs'>Editar</a>";
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

    public function devolverLivro($IDEmprestimo){
        Emprestimo::find($IDEmprestimo)->update([
            "STEmprestimo" => 1
        ]);

        return redirect()->back();
    }

    public function getEmprestimos(){
        $IDOrg = Auth::user()->id_org;
        $SQL = <<<SQL
        SELECT
            em.id,
            lei.Nome as Leitor,
            l.Nome as Livro,
            em.created_at as Emprestimo,
            em.STEmprestimo,
            em.Devolucao
        FROM emprestimos em
        INNER JOIN livros l ON(em.IDLivro = l.id)
        INNER JOIN leitores lei ON(lei.id = em.IDLeitor) 
        INNER JOIN cod_livros cl ON(cl.id = l.IDCodigo)
        WHERE cl.IDOrg = $IDOrg
        SQL;

        $rows = DB::select($SQL);
        $hoje = date('Y-m-d');
        if(count($rows) > 0){
            foreach($rows as $r){

                if($r->STEmprestimo == 0 && Carbon::parse($r->Devolucao)->gt(Carbon::parse($hoje))){
                    $STEmprestimo = "Atrasado";
                }elseif($r->STEmprestimo == 0){
                    $STEmprestimo = "Em Uso";
                }else{
                    $STEmprestimo = "Devolvido";
                }

                $Btn = "<a href='".route('Biblioteca/Emprestimos/Edit',$r->id)."' class='btn btn-primary btn-xs'>Editar</a>";

                if($r->STEmprestimo == 0){
                    $Btn .= "&nbsp;<a href='".route('Biblioteca/DevolverLivro',$r->id)."' class='btn btn-secondary btn-xs'>Devolver</a>";
                }

                $item = [];
                $item[] = $r->Leitor;
                $item[] = $r->Livro;
                $item[] = $r->Emprestimo;
                $item[] = $r->Devolucao;
                $item[] = $STEmprestimo;
                $item[] = $Btn;
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

    public function getLeitores(){
        $IDOrg = Auth::user()->id_org;
        $SQL = <<<SQL
        SELECT 
            l.Nome as Leitor,
            l.EnderecoJSON,
            l.Cargo,
            l.id
        FROM leitores l
        WHERE l.IDOrg = $IDOrg
        SQL;

        $rows = DB::select($SQL);
        if(count($rows) > 0){
            foreach($rows as $r){
                $e = json_decode($r->EnderecoJSON);
                $item = [];
                $item[] = $r->Leitor;
                $item[] = $e->Rua.", ".$e->Numero." ".$e->Bairro." ".$e->Cidade." - ".$e->UF;
                $item[] = $r->Cargo;
                $item[] = "<a href='".route('Biblioteca/Leitores/Edit',$r->id)."' class='btn btn-primary btn-xs'>Editar</a>";
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

    public function cadastro($id=null){
        $IDOrg = Auth::user()->id_org;
        $Codigos = DB::select("SELECT 
            Codigo,id 
        FROM cod_livros cdl
        WHERE 
        cdl.id NOT IN(SELECT IDCodigo FROM livros INNER JOIN cod_livros ON(cod_livros.id = livros.IDCodigo) WHERE cod_livros.IDOrg = $IDOrg)
        ");
        
        $view = [
            "submodulos" => self::submodulos,
            "id" => "",
            "codLivros" => $Codigos
        ];

        if($id){
            $view['Registro'] = Livro::find($id);
            $view['id'] = $id;
        }

        return view('Biblioteca.cadastro',$view);
    }

    public function gerarEtiquetas(){
        // Crie uma instância do FPDF
        $pdf = new Fpdf();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 12);

        // Configuração do layout da folha
        $marginLeft = 10;
        $marginTop = 10;
        $spaceX = 50; // Espaçamento horizontal
        $spaceY = 35; // Espaçamento vertical
        $codesPerRow = 4;
        $row = 0;
        $col = 0;

        // Instância do gerador de códigos de barras
        $generator = new BarcodeGeneratorPNG();

        // Gere múltiplos códigos EAN-13
        for ($i = 1; $i <= 20; $i++) {
            // Número base do código EAN-13 (12 dígitos)
            // Gere um número aleatório de 12 dígitos
            $baseCode = str_pad(rand(0, 999999999999), 12, '0', STR_PAD_LEFT);

            // Calcular o dígito verificador
            $ean13Code = $baseCode . $this->calculateChecksum($baseCode);
            BARCode::create([
                "Codigo" => $ean13Code,
                "IDOrg" => Auth::user()->id_org
            ]);
            // Gere a imagem do código de barras como uma string PNG
            $barcode = $generator->getBarcode($ean13Code, $generator::TYPE_EAN_13);

            // Salve o PNG temporariamente
            $barcodePath = storage_path("app/public/barcode_$i.png");
            file_put_contents($barcodePath, $barcode);

            // Adicione o código de barras no PDF
            $x = $marginLeft + ($col * $spaceX);
            $y = $marginTop + ($row * $spaceY);
            $pdf->Image($barcodePath, $x, $y, 40, 20); // Ajuste o tamanho conforme necessário
            $pdf->Text($x, $y + 25, $ean13Code); // Número abaixo do código de barras

            // Controle de coluna e linha
            $col++;
            if ($col == $codesPerRow) {
                $col = 0;
                $row++;
            }
        }

        // Exclua as imagens temporárias
        for ($i = 1; $i <= 20; $i++) {
            @unlink(storage_path("app/public/barcode_$i.png"));
        }

        $pdf->Output('Etiquetas dos Livros_'.date('Y-m-d  H:i:s').'.pdf','D');
        exit;
    }

    private function calculateChecksum($baseCode)
    {
        $sum = 0;

        // Itera sobre os 12 dígitos para calcular o checksum
        for ($i = 0; $i < 12; $i++) {
            $digit = (int)$baseCode[$i];
            $sum += $i % 2 === 0 ? $digit : $digit * 3;
        }

        // Retorna o número necessário para completar o múltiplo de 10
        return (10 - ($sum % 10)) % 10;
    }

    public function cadastroEmprestimos($id=null){
        $IDOrg = Auth::user()->id_org;
        $SQLLivros = <<<SQL
        SELECT
            l.Nome as Livro,
            l.Autor,
            l.Classificacao,
            l.Editora,
            l.id,
            cl.Codigo
        FROM livros l 
        INNER JOIN cod_livros cl ON(cl.id = l.IDCodigo)
        WHERE cl.IDOrg = $IDOrg AND l.id NOT IN(SELECT IDLivro FROM emprestimos WHERE STEmprestimo = 0)
        SQL;

        $view = [
            "submodulos" => self::submodulos,
            "id" => "",
            "Leitor" => Leitor::where('IDOrg',$IDOrg)->get(),
            "Livros" => DB::select($SQLLivros)
        ];

        if($id){
            $view['Registro'] = Emprestimo::find($id);
            $view['id'] = $id;
        }

        return view('Biblioteca.cadastroEmprestimo',$view);
    }

    public function cadastroLeitores($id=null){
        $view = [
            "submodulos" => self::submodulos,
            "id" => ""
        ];

        if($id){
            $Leitor = Leitor::find($id);
            $view['Registro'] = $Leitor;
            $view['Endereco'] = json_decode($Leitor->EnderecoJSON);
            $view['id'] = $id;
        }

        return view('Biblioteca.cadastroLeitor',$view);
    }
}
