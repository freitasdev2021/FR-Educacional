<?php

namespace App\Http\Controllers;
use App\Http\Controllers\EscolasController;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Models\Escola;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RelatoriosController extends Controller
{

    public const submodulos = EscolasController::submodulos; 

    public function imprimir($Tipo){
        switch($Tipo){
            case 'Ocorrencias':
                $view = 'Escolas.Relatorios.ocorrencias';
            break;
            case 'Transferidos':
                $view = 'Escolas.Relatorios.transferidos';
            break;
            case 'Remanejados':
                $view = 'Escolas.Relatorios.remanejados';
            break;
            case 'Evadidos':
                $view = 'Escolas.Relatorios.evadidos';
            break;
            case 'Responsaveis':
                $view = 'Escolas.Relatorios.responsaveis';
            break;
            case 'TurmaFaixa':
                $view = 'Escolas.Relatorios.turmaFaixa';
            break;
            case 'QTTransporte':
                $view = 'Escolas.Relatorios.quantidadeTransporte';
            break;
            case 'NMTransporte':
                $view = 'Escolas.Relatorios.usuariosTransporte';
            break;
        }
        return view($view,[
            'submodulos' => self::submodulos,
            'Tipo' => $Tipo
        ]);
    }

    public function Gerar(Request $request,$Tipo){
        try{
            switch($Tipo){
                case 'Ocorrencias':
                    self::QTOcorrencias($request->Conteudo);
                break;
                case 'Transferidos':
                    self::QTTransferidos($request->Conteudo);
                break;
                case 'Remanejados':
                    self::QTRemanejados($request->Conteudo);
                break;
                case 'Evadidos':
                    self::QTEvadidos($request->Conteudo);
                break;
                case 'Responsaveis':
                    self::Responsaveis($request->Conteudo);
                break;
                case 'TurmaFaixa':
                    self::QTTurmaFaixa($request->Conteudo);
                break;
                case 'QTTransporte':
                    self::QTTransporte($request->Conteudo);
                break;
                case 'NMTransporte':
                    self::getTransporte($request->Conteudo);
                break;
            }
        }catch(\Throwable $th){

        }finally{

        }
    }

    public function getTransporte($Conteudo){
        // Criar o PDF com FPDF
        $pdf = new FPDF();
        $pdf->AddPage(); // Adiciona uma página

        // Definir margens
        $pdf->SetMargins(10, 10, 10);

        // Definir cabeçalho do relatório
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, self::utfConvert("Alunos Usuários de Transporte"), 0, 1, 'C');
        $pdf->Ln(10); // Espaço após o título

        // Definir fonte para o corpo do relatório
        $pdf->SetFont('Arial', '', 12);
        $IDEscola = self::getEscolaDiretor(Auth::user()->id);
        $Transporte = DB::select("SELECT 
            m.Nome,
            t.Nome as Turma
        FROM alunos a 
        INNER JOIN matriculas m ON(a.IDMatricula = m.id) 
        INNER JOIN turmas t ON(a.IDTurma = t.id) 
        INNER JOIN escolas e ON(e.id = t.IDEscola) 
        WHERE e.id = $IDEscola");

        // Exibir o cabeçalho escolhido
        foreach($Conteudo as $c){
            $pdf->Cell(80, 10, mb_convert_encoding($c, 'ISO-8859-1', 'UTF-8'), 1);
        }
        $pdf->Ln(); // Adicionar linha nova após o cabeçalho

        // Preencher a tabela com os dados das escolas
        $pdf->SetFont('Arial', '', 12);
        foreach ($Transporte as $escola) {
            // Verifica e imprime as células de acordo com os valores selecionados em $Conteudo
            if (in_array('Nome', $Conteudo)) {
                $pdf->Cell(80, 10, mb_convert_encoding($escola->Nome, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            }
            if (in_array('Turma', $Conteudo)) {
                $pdf->Cell(50, 10, mb_convert_encoding($escola->Turma, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            }

            // Adicionar nova linha após a célula
            $pdf->Ln();
        }

        $pdf->Output('D', 'Usuarios_Transporte.pdf');
        exit;
    }

    public function QTTransporte($Conteudo){
       // Criar o PDF com FPDF
       $pdf = new FPDF();
       $pdf->AddPage(); // Adiciona uma página

       // Definir margens
       $pdf->SetMargins(10, 10, 10);

       // Definir cabeçalho do relatório
       $pdf->SetFont('Arial', 'B', 16);
       $pdf->Cell(0, 10, self::utfConvert(self::utfConvert("Relatório sobre usuários de Transporte")), 0, 1, 'C');
       $pdf->Ln(10); // Espaço após o título

       // Definir fonte para o corpo do relatório
       $pdf->SetFont('Arial', '', 12);
       $IDEscola = self::getEscolaDiretor(Auth::user()->id);
       $Quantidade = DB::select("SELECT 
            COUNT(a.id) as Quantidade 
        FROM alunos a
        INNER JOIN matriculas m ON(a.IDMatricula = m.id) 
        INNER JOIN turmas t ON(a.IDTurma = t.id) 
        INNER JOIN escolas e ON(e.id = t.IDEscola) WHERE e.id = $IDEscola ")[0]->Quantidade;
       // CONTEÚDO DO PDF
       $pdf->Cell(0, 10, 'Atualmente Há' . $Quantidade. " Alunos Utilizando Transporte nessa Instituição", 0, 1);
       $pdf->Ln(5); // Espaço após as informações da aula

       // HORÁRIO
       $pdf->Cell(0, 10, date('d/m/Y - H:i:s'), 0, 1, 'L');

       // Gera o PDF para saída
       $pdf->Output('D','Quantidade Transporte.pdf');
       exit;
   }


    public function QTTurmaFaixa($Conteudo){
         // Criar instância do FPDF
         $pdf = new Fpdf();

         // Adicionar uma página
         $pdf->AddPage();
 
         // Definir o papel timbrado da escola (descomente se necessário)
         //$this->adicionarPapelTimbrado($pdf);
 
         // Definir título
         $pdf->SetFont('Arial', 'B', 14);
         $pdf->Cell(0, 10, mb_convert_encoding('Alunos por Turma e Faixa', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
 
         // Espaçamento
         $pdf->Ln(10);
 
         // Cabeçalho da tabela
         $pdf->SetFont('Arial', 'B', 12);
         foreach($Conteudo as $c){
             $pdf->Cell(80, 10, mb_convert_encoding($c, 'ISO-8859-1', 'UTF-8'), 1);
         }
         $pdf->Ln();
         $IDEscola = self::getEscolaDiretor(Auth::user()->id);
         // Recuperar lista de escolas e quantidade de alunos transferidos
         $IDOrg = Auth::user()->id_org;
         $escolas = DB::select("SELECT COUNT(m.Nome) as QTSerie,t.Serie FROM alunos a INNER JOIN matriculas m ON(a.IDMatricula = m.id) INNER JOIN turmas t ON(a.IDTurma = t.id) INNER JOIN escolas e ON(e.id = t.IDEscola) = e.IDOrg = $IDEscola GROUP BY t.Serie");
 
         // Preencher a tabela com os dados das escolas
         $pdf->SetFont('Arial', '', 12);
         foreach ($escolas as $escola) {
             in_array('Serie',$Conteudo) ? $pdf->Cell(80, 10, mb_convert_encoding($escola->Serie, 'ISO-8859-1', 'UTF-8'), 1) : '';
             in_array('QTSerie',$Conteudo) ? $pdf->Cell(50, 10, $escola->QTSerie, 1) : '';
             // Adicionar espaço após cada linha
             $pdf->Ln(0); // Não adicionar nova linha, pois o MultiCell já faz isso
         }
 
         // Saída do PDF
         $pdf->Output('D', 'relatorio_turmafaixa.pdf');
    }

    public function QTTransferidos($Conteudo){
        // Criar instância do FPDF
        $pdf = new Fpdf();

        // Adicionar uma página
        $pdf->AddPage();

        // Definir o papel timbrado da escola (descomente se necessário)
        //$this->adicionarPapelTimbrado($pdf);

        // Definir título
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, mb_convert_encoding('Alunos Transferidos', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        // Espaçamento
        $pdf->Ln(10);

        // Cabeçalho da tabela
        $pdf->SetFont('Arial', 'B', 12);
        foreach($Conteudo as $c){
            $pdf->Cell(80, 10, mb_convert_encoding($c, 'ISO-8859-1', 'UTF-8'), 1);
        }
        $pdf->Ln();

        // Recuperar lista de escolas e quantidade de alunos transferidos
        $IDOrg = Auth::user()->id_org;
        $escolas = DB::select("SELECT e.Nome, COUNT(t.id) as QTTransferidos, e.Rua, e.Cidade, e.Bairro, e.UF, e.Numero FROM escolas e INNER JOIN transferencias t ON (t.IDEscolaOrigem = e.id) WHERE e.IDOrg = $IDOrg GROUP BY e.Nome");

        // Preencher a tabela com os dados das escolas
        $pdf->SetFont('Arial', '', 12);
        foreach ($escolas as $escola) {
            in_array('Nome da Escola',$Conteudo) ? $pdf->Cell(80, 10, mb_convert_encoding($escola->Nome, 'ISO-8859-1', 'UTF-8'), 1) : '';
            in_array('Alunos Transferidos',$Conteudo) ? $pdf->Cell(50, 10, $escola->QTTransferidos, 1) : '';
            
            // Combinar o endereço completo e quebrar linha automaticamente
            $endereco = mb_convert_encoding($escola->Rua, 'ISO-8859-1', 'UTF-8') . ' ,' . $escola->Numero . "\n" .
                        mb_convert_encoding($escola->Bairro, 'ISO-8859-1', 'UTF-8') . "\n" .
                        mb_convert_encoding($escola->Cidade, 'ISO-8859-1', 'UTF-8') . '/' . 
                        mb_convert_encoding($escola->UF, 'ISO-8859-1', 'UTF-8');

            // Usar MultiCell para quebrar linha no endereço
            in_array('Endereço',$Conteudo) ? $pdf->MultiCell(50, 10, $endereco, 1) : '';

            // Adicionar espaço após cada linha
            $pdf->Ln(0); // Não adicionar nova linha, pois o MultiCell já faz isso
        }

        // Saída do PDF
        $pdf->Output('D', 'relatorio_transferidos.pdf');
    }

    public function QTOcorrencias($Conteudo){
        // Criar instância do FPDF
        $pdf = new Fpdf();

        // Adicionar uma página
        $pdf->AddPage();

        // Definir o papel timbrado da escola (descomente se necessário)
        //$this->adicionarPapelTimbrado($pdf);

        // Definir título
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, mb_convert_encoding('Ocorrências por Escola', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        // Espaçamento
        $pdf->Ln(10);

        // Cabeçalho da tabela
        $pdf->SetFont('Arial', 'B', 12);
        foreach($Conteudo as $c){
            $pdf->Cell(80, 10, mb_convert_encoding($c, 'ISO-8859-1', 'UTF-8'), 1);
        }
        $pdf->Ln();

        // Recuperar lista de escolas e quantidade de alunos transferidos
        $IDOrg = Auth::user()->id_org;
        $escolas = DB::select("SELECT 
            e.Nome, 
            COUNT(o.id) as QTOcorrencias, 
            e.Rua, 
            e.Cidade, 
            e.Bairro, 
            e.UF, 
            e.Numero 
            FROM escolas e 
            INNER JOIN ocorrencias o ON (e.id = o.IDEscola) 
            WHERE e.IDOrg = $IDOrg GROUP BY e.Nome
            ");

        // Preencher a tabela com os dados das escolas
        $pdf->SetFont('Arial', '', 12);
        foreach ($escolas as $escola) {
            in_array('Nome da Escola',$Conteudo) ? $pdf->Cell(80, 10, mb_convert_encoding($escola->Nome, 'ISO-8859-1', 'UTF-8'), 1) : '';
            in_array('Ocorrências',$Conteudo) ? $pdf->Cell(50, 10, $escola->QTOcorrencias, 1) : '';
            
            // Combinar o endereço completo e quebrar linha automaticamente
            $endereco = mb_convert_encoding($escola->Rua, 'ISO-8859-1', 'UTF-8') . ' ,' . $escola->Numero . "\n" .
                        mb_convert_encoding($escola->Bairro, 'ISO-8859-1', 'UTF-8') . "\n" .
                        mb_convert_encoding($escola->Cidade, 'ISO-8859-1', 'UTF-8') . '/' . 
                        mb_convert_encoding($escola->UF, 'ISO-8859-1', 'UTF-8');

            // Usar MultiCell para quebrar linha no endereço
            in_array('Endereço',$Conteudo) ? $pdf->MultiCell(50, 10, $endereco, 1) : '';

            // Adicionar espaço após cada linha
            $pdf->Ln(0); // Não adicionar nova linha, pois o MultiCell já faz isso
        }

        // Saída do PDF
        $pdf->Output('D', 'relatorio_ocorrencias.pdf');
    }

    public function QTRemanejados($Conteudo){
        // Criar instância do FPDF
        $pdf = new Fpdf();

        // Adicionar uma página
        $pdf->AddPage();

        // Definir o papel timbrado da escola (descomente se necessário)
        //$this->adicionarPapelTimbrado($pdf);

        // Definir título
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, mb_convert_encoding('Remanejados por Escola', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        // Espaçamento
        $pdf->Ln(10);

        // Cabeçalho da tabela
        $pdf->SetFont('Arial', 'B', 12);
        foreach($Conteudo as $c){
            $pdf->Cell(80, 10, mb_convert_encoding($c, 'ISO-8859-1', 'UTF-8'), 1);
        }
        $pdf->Ln();

        // Recuperar lista de escolas e quantidade de alunos transferidos
        $IDOrg = Auth::user()->id_org;
        $escolas = DB::select("SELECT 
            e.Nome, 
            COUNT(r.id) as QTRemanejados, 
            e.Rua, 
            e.Cidade, 
            e.Bairro, 
            e.UF, 
            e.Numero 
            FROM escolas e 
            INNER JOIN remanejados r ON (e.id = r.IDEscola) 
            WHERE e.IDOrg = $IDOrg GROUP BY e.Nome
            ");

        // Preencher a tabela com os dados das escolas
        $pdf->SetFont('Arial', '', 12);
        foreach ($escolas as $escola) {
            in_array('Nome da Escola',$Conteudo) ? $pdf->Cell(80, 10, mb_convert_encoding($escola->Nome, 'ISO-8859-1', 'UTF-8'), 1) : '';
            in_array('Remanejados',$Conteudo) ? $pdf->Cell(50, 10, $escola->QTRemanejados, 1) : '';
            
            // Combinar o endereço completo e quebrar linha automaticamente
            $endereco = mb_convert_encoding($escola->Rua, 'ISO-8859-1', 'UTF-8') . ' ,' . $escola->Numero . "\n" .
                        mb_convert_encoding($escola->Bairro, 'ISO-8859-1', 'UTF-8') . "\n" .
                        mb_convert_encoding($escola->Cidade, 'ISO-8859-1', 'UTF-8') . '/' . 
                        mb_convert_encoding($escola->UF, 'ISO-8859-1', 'UTF-8');

            // Usar MultiCell para quebrar linha no endereço
            in_array('Endereço',$Conteudo) ? $pdf->MultiCell(50, 10, $endereco, 1) : '';

            // Adicionar espaço após cada linha
            $pdf->Ln(0); // Não adicionar nova linha, pois o MultiCell já faz isso
        }

        // Saída do PDF
        $pdf->Output('D', 'relatorio_remanejados.pdf');
    }

    public function Responsaveis($Conteudo){
        // Criar instância do FPDF
        $pdf = new Fpdf();

        // Adicionar uma página
        $pdf->AddPage();

        // Definir o papel timbrado da escola (descomente se necessário)
        //$this->adicionarPapelTimbrado($pdf);
        $pdf->SetMargins(5, 5, 5);
        // Definir título
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, mb_convert_encoding('Lista de Responsaveis', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        // Espaçamento
        $pdf->Ln(10);

        // Cabeçalho da tabela
        $pdf->SetFont('Arial', 'B', 12);
        foreach($Conteudo as $c){
            $pdf->Cell(50, 7, mb_convert_encoding($c, 'ISO-8859-1', 'UTF-8'), 1);
        }
        $pdf->Ln();

        // Recuperar lista de escolas e quantidade de alunos transferidos
        $IDOrg = Auth::user()->id_org;
        $escolas = DB::select("SELECT 
            e.Nome as Escola, 
            m.Nome as Aluno,
            r.NMResponsavel as Responsavel,
            m.Celular as Telefone
            FROM matriculas m 
            INNER JOIN alunos a ON (m.id = a.IDMatricula)
            INNER JOIN responsavel r ON(r.IDAluno = a.id)
            INNER JOIN turmas t ON(t.id = a.IDTurma)
            INNER JOIN escolas e ON(t.IDEscola = e.id)
            WHERE e.IDOrg = $IDOrg
            ");

        // Preencher a tabela com os dados das escolas
        $pdf->SetFont('Arial', '', 8);
        foreach ($escolas as $escola) {
            in_array('Escola',$Conteudo) ? $pdf->Cell(50, 7, mb_convert_encoding($escola->Escola, 'ISO-8859-1', 'UTF-8'), 1) : '';
            in_array('Aluno',$Conteudo) ? $pdf->Cell(50, 7, $escola->Aluno, 1) : '';
            in_array('Responsavel',$Conteudo) ? $pdf->Cell(50, 7, $escola->Responsavel, 1) : '';
            in_array('Telefone',$Conteudo) ? $pdf->Cell(50, 7, $escola->Telefone, 1) : '';
            // Adicionar espaço após cada linha
            $pdf->Ln(); // Não adicionar nova linha, pois o MultiCell já faz isso
        }

        // Saída do PDF
        $pdf->Output('D', 'relatorio_responsaveis.pdf');
    }

    public function QTEvadidos($Conteudo){
        // Criar instância do FPDF
        $pdf = new Fpdf();

        // Adicionar uma página
        $pdf->AddPage();

        // Definir o papel timbrado da escola (descomente se necessário)
        //$this->adicionarPapelTimbrado($pdf);

        // Definir título
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, mb_convert_encoding('Evadidos por Escola', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        // Espaçamento
        $pdf->Ln(10);

        // Cabeçalho da tabela
        $pdf->SetFont('Arial', 'B', 12);
        foreach($Conteudo as $c){
            $pdf->Cell(80, 10, mb_convert_encoding($c, 'ISO-8859-1', 'UTF-8'), 1);
        }
        $pdf->Ln();

        // Recuperar lista de escolas e quantidade de alunos transferidos
        $IDOrg = Auth::user()->id_org;
        $escolas = DB::select("SELECT 
            e.Nome, 
            COUNT(a.id) as QTEvadidos, 
            e.Rua, 
            e.Cidade, 
            e.Bairro, 
            e.UF, 
            e.Numero 
            FROM escolas e 
            INNER JOIN turmas t ON (e.id = t.IDEscola)
            INNER JOIN alunos a ON(a.IDTurma = t.id)
            WHERE e.IDOrg = $IDOrg AND a.STAluno = 1 GROUP BY e.Nome, e.Rua, e.Cidade, e.Bairro, e.UF, e.Numero
            ");

        // Preencher a tabela com os dados das escolas
        $pdf->SetFont('Arial', '', 12);
        foreach ($escolas as $escola) {
            in_array('Nome da Escola',$Conteudo) ? $pdf->Cell(80, 10, mb_convert_encoding($escola->Nome, 'ISO-8859-1', 'UTF-8'), 1) : '';
            in_array('Evadidos',$Conteudo) ? $pdf->Cell(50, 10, $escola->QTEvadidos, 1) : '';
            
            // Combinar o endereço completo e quebrar linha automaticamente
            $endereco = mb_convert_encoding($escola->Rua, 'ISO-8859-1', 'UTF-8') . ' ,' . $escola->Numero . "\n" .
                        mb_convert_encoding($escola->Bairro, 'ISO-8859-1', 'UTF-8') . "\n" .
                        mb_convert_encoding($escola->Cidade, 'ISO-8859-1', 'UTF-8') . '/' . 
                        mb_convert_encoding($escola->UF, 'ISO-8859-1', 'UTF-8');

            // Usar MultiCell para quebrar linha no endereço
            in_array('Endereço',$Conteudo) ? $pdf->MultiCell(50, 10, $endereco, 1) : '';

            // Adicionar espaço após cada linha
            $pdf->Ln(0); // Não adicionar nova linha, pois o MultiCell já faz isso
        }

        // Saída do PDF
        $pdf->Output('D', 'relatorio_evadidos.pdf');
    }

    private function adicionarPapelTimbrado(Fpdf $pdf,$image)
    {
        // Adicionar imagem do papel timbrado (caminho da imagem)
        $pdf->Image(public_path($image), 10, 10, 190);
        $pdf->Ln(30);  // Adiciona espaço após a imagem
    }
}
