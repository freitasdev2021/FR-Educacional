<?php

namespace App\Http\Controllers;
use App\Http\Controllers\EscolasController;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Models\Escola;
use App\Models\Sala;
use App\Models\Turma;
use Carbon\Carbon;
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
            case 'Lista de Turmas':
                $view = 'Escolas.Relatorios.Turmas';
            break;
        }
        return view($view,[
            'submodulos' => self::submodulos,
            'Tipo' => $Tipo
        ]);
    }

    public function imprimirDireto($Tipo){
        switch($Tipo){
            case "Dependencias Escola":
            return  self::dependenciasEscola();
            case "Lista de Turmas":
            return  self::getAlunosTurmas();
            case "Alunos por Sexo":
            return  self::QTAlunosSexo();
            case "NMTransporte":
            return self::getTransporte();
            case "BolsaFamilia":
            return self::getBolsaFamilia();
            case "getRecuperacaoFinal":
            return self::getRecuperacaoFinal();
            case "getQTRecuperacaoFinal":
            return self::getQTRecuperacaoFinal();
            case "getRecuperacaoFinalFaltas":
            return self::getRecuperacaoFinalFaltas();
        }
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
            }
        }catch(\Throwable $th){

        }finally{

        }
    }

    public function getQTRecuperacaoFinal(){
        $ANO = date('Y');
        $WHERE = "WHERE ";
        if(in_array(Auth::user()->tipo,[2,2.5])){
            $WHERE .= "e.IDOrg=".Auth::user()->id_org;;
        }else{
            $WHERE .="e.id = ".self::getEscolaDiretor(Auth::user()->id);
        }
        $SQLTurmas = "SELECT t.id as IDTurma,t.Nome as Turma,t.Serie,e.Nome as Escola,t.MediaPeriodo FROM turmas t INNER JOIN escolas e ON(e.id = t.IDEscola) $WHERE";

        $Turmas = DB::select($SQLTurmas);

        $pdf = new FPDF();
        $pdf->AddPage();
        // Definir margens
        $pdf->SetMargins(3, 3, 3); // Margens esquerda, superior e direita

        $pdf->SetFont('Arial', 'B', 16);

        $pdf->Cell(0, 10, self::utfConvert("Alunos em Recuperação Final por Turma"), 0, 1, 'C'); // Nome da escola centralizado
        $pdf->Ln(10);

        foreach($Turmas as $t){
            
            $SQL = <<<SQL
            SELECT 
                (SELECT SUM(n2.Nota) 
                FROM notas n2 
                INNER JOIN atividades at2 ON n2.IDAtividade = at2.id 
                INNER JOIN aulas au3 ON at2.IDAula = au3.id 
                WHERE au3.IDDisciplina = d.id 
                AND n2.IDAluno = a.id 
                AND DATE_FORMAT(au3.created_at, '%Y') = $ANO
                ) as TotalAno,

                (SELECT SUM(rec2.Nota) 
                FROM recuperacao rec2 
                WHERE rec2.Estagio != 'ANUAL' 
                AND rec2.IDAluno = a.id 
                AND rec2.IDDisciplina = d.id 
                ) as RecBim,

                -- Frequência (quantidade de presenças) do Aluno para a Disciplina e Estágio específicos
                (SELECT COUNT(f2.id) 
                FROM frequencia f2 
                INNER JOIN aulas au2 ON au2.id = f2.IDAula 
                WHERE f2.IDAluno = a.id 
                AND au2.IDDisciplina = d.id 
                AND DATE_FORMAT(au2.created_at, '%Y') = $ANO
                ) as FrequenciaAno,
                (SELECT SUM(rec2.Nota) FROM recuperacao rec2 WHERE rec2.Estagio != 'ANUAL' AND rec2.IDAluno = a.id AND rec2.IDDisciplina = d.id ) as RecBim,
                (SELECT SUM(rec2.PontuacaoPeriodo) FROM recuperacao rec2 WHERE rec2.Estagio != 'ANUAL' AND rec2.IDAluno = a.id AND rec2.IDDisciplina = d.id ) as PontBim,
                -- Caso em que é verificado se a nota é inferior à média
                CASE WHEN 
                    (SELECT SUM(n2.Nota) 
                    FROM notas n2 
                    INNER JOIN atividades at2 ON n2.IDAtividade = at2.id 
                    INNER JOIN aulas au3 ON at2.IDAula = au3.id 
                    WHERE au3.IDDisciplina = 10 
                    AND n2.IDAluno = a.id 
                    AND DATE_FORMAT(n2.created_at, '%Y') = $ANO
                    ) < t.MediaPeriodo THEN 'Reprovado' 
                ELSE 'Aprovado' END as Resultado,
                m.Nome as Aluno,         -- Nome do Aluno
                a.id as IDAluno,         -- ID do Aluno
                d.NMDisciplina as Disciplina, -- Nome da Disciplina
                t.MediaPeriodo,
                t.TPAvaliacao,
                t.MINFrequencia,
                t.Serie
            FROM 
                alunos a
            INNER JOIN 
                matriculas m ON m.id = a.IDMatricula   -- Relaciona alunos com suas matrículas
            INNER JOIN 
                turmas t ON t.id = a.IDTurma           -- Relaciona alunos com suas turmas
            INNER JOIN 
                aulas au ON t.id = au.IDTurma          -- Relaciona turmas com aulas
            INNER JOIN 
                disciplinas d ON d.id = au.IDDisciplina -- Relaciona aulas com disciplinas
            INNER JOIN 
                notas n ON n.IDAluno = a.id            -- Relaciona notas com alunos
            WHERE 
                DATE_FORMAT(au.created_at, '%Y') = $ANO AND a.IDTurma = $t->IDTurma
            GROUP BY 
                a.id, d.id, m.Nome, t.MediaPeriodo, t.TPAvaliacao, t.MINFrequencia
            SQL;

            $QueryRec = DB::select($SQL);
            $Recuperacoes = [];
            foreach($QueryRec as $r){
                $Frequencia = ($r->FrequenciaAno / 200) * 100 . " %";
                $MediaTotal = $r->MediaPeriodo * 4;       
                if($r->TPAvaliacao == "Nota"){
                    if($r->RecBim > 0){
                        $Total = ($r->RecBim - $r->TotalAno) + $r->PontBim;
                        //dd($Total);
                    }else{
                        $Total = $r->TotalAno;
                    }
                    
                    $Resultado = ($Total > $MediaTotal) ? 'Aprovado' : 'Reprovado';
                }else{
                    $Resultado = "Conceito Sob. Avaliação";
                }

                if (!isset($Recuperacoes[$t->Serie])) {
                    $Recuperacoes[$t->Serie] = []; // Inicializa o array para o aluno se não existir
                }

                if($Resultado == "Reprovado"){
                    $Recuperacoes[$t->Serie][] = $r->Aluno;
                }
            }

            //dd($SQL);

            // Definir fonte para o corpo do relatório
            //CABECALHO DA TABELA
            $pdf->SetFont('Arial', '', 8);
            //CORPO DA LISTA    
            foreach($Recuperacoes as $key => $r){
                $pdf->Cell(65, 8, self::utfConvert("Turma: ".$key), 1);
                $pdf->Cell(75, 8, self::utfConvert("Quantidade: ".count($r)), 1);
                $pdf->Ln();
            }
        }

        $pdf->Ln();
        $pdf->Cell(0, 10, self::utfConvert("Emitido Por ".Auth::user()->name." no Dia ".date('d/m/Y H:i')), 0, 1, 'C');
        //DOWNLOAD
        $pdf->Output('I',"Alunos Recuperacao".'.pdf');
        exit;
    }

    public function getRecuperacaoFinal(){
        $ANO = date('Y');
        $WHERE = "WHERE ";
        if(in_array(Auth::user()->tipo,[2,2.5])){
            $WHERE .= "e.IDOrg=".Auth::user()->id_org;;
        }else{
            $WHERE .="e.id = ".self::getEscolaDiretor(Auth::user()->id);
        }
        $SQLTurmas = "SELECT t.id as IDTurma,t.Nome as Turma,t.Serie,e.Nome as Escola,t.MediaPeriodo FROM turmas t INNER JOIN escolas e ON(e.id = t.IDEscola) $WHERE";

        $Turmas = DB::select($SQLTurmas);

        $pdf = new FPDF();
        $pdf->AddPage();
        // Definir margens
        $pdf->SetMargins(3, 3, 3); // Margens esquerda, superior e direita

        $pdf->SetFont('Arial', 'B', 16);

        $pdf->Cell(0, 10, self::utfConvert("Alunos em Recuperação Final"), 0, 1, 'C'); // Nome da escola centralizado
        $pdf->Ln(10);

        $pageCount = 0;
        foreach($Turmas as $t){
            if ($pageCount % 1 == 0 && $pageCount > 0) {
                $pdf->AddPage();
            }

            $SQL = <<<SQL
            SELECT 
                (SELECT SUM(n2.Nota) 
                FROM notas n2 
                INNER JOIN atividades at2 ON n2.IDAtividade = at2.id 
                INNER JOIN aulas au3 ON at2.IDAula = au3.id 
                WHERE au3.IDDisciplina = d.id 
                AND n2.IDAluno = a.id 
                AND DATE_FORMAT(au3.created_at, '%Y') = $ANO
                ) as TotalAno,

                (SELECT SUM(rec2.Nota) 
                FROM recuperacao rec2 
                WHERE rec2.Estagio != 'ANUAL' 
                AND rec2.IDAluno = a.id 
                AND rec2.IDDisciplina = d.id 
                ) as RecBim,

                -- Frequência (quantidade de presenças) do Aluno para a Disciplina e Estágio específicos
                (SELECT COUNT(f2.id) 
                FROM frequencia f2 
                INNER JOIN aulas au2 ON au2.id = f2.IDAula 
                WHERE f2.IDAluno = a.id 
                AND au2.IDDisciplina = d.id 
                AND DATE_FORMAT(au2.created_at, '%Y') = $ANO
                ) as FrequenciaAno,
                (SELECT SUM(rec2.Nota) FROM recuperacao rec2 WHERE rec2.Estagio != 'ANUAL' AND rec2.IDAluno = a.id AND rec2.IDDisciplina = d.id ) as RecBim,
                (SELECT SUM(rec2.PontuacaoPeriodo) FROM recuperacao rec2 WHERE rec2.Estagio != 'ANUAL' AND rec2.IDAluno = a.id AND rec2.IDDisciplina = d.id ) as PontBim,
                -- Caso em que é verificado se a nota é inferior à média
                CASE WHEN 
                    (SELECT SUM(n2.Nota) 
                    FROM notas n2 
                    INNER JOIN atividades at2 ON n2.IDAtividade = at2.id 
                    INNER JOIN aulas au3 ON at2.IDAula = au3.id 
                    WHERE au3.IDDisciplina = 10 
                    AND n2.IDAluno = a.id 
                    AND DATE_FORMAT(n2.created_at, '%Y') = $ANO
                    ) < t.MediaPeriodo THEN 'Reprovado' 
                ELSE 'Aprovado' END as Resultado,
                m.Nome as Aluno,         -- Nome do Aluno
                a.id as IDAluno,         -- ID do Aluno
                d.NMDisciplina as Disciplina, -- Nome da Disciplina
                t.MediaPeriodo,
                t.TPAvaliacao,
                t.MINFrequencia
            FROM 
                alunos a
            INNER JOIN 
                matriculas m ON m.id = a.IDMatricula   -- Relaciona alunos com suas matrículas
            INNER JOIN 
                turmas t ON t.id = a.IDTurma           -- Relaciona alunos com suas turmas
            INNER JOIN 
                aulas au ON t.id = au.IDTurma          -- Relaciona turmas com aulas
            INNER JOIN 
                disciplinas d ON d.id = au.IDDisciplina -- Relaciona aulas com disciplinas
            INNER JOIN 
                notas n ON n.IDAluno = a.id            -- Relaciona notas com alunos
            WHERE 
                DATE_FORMAT(au.created_at, '%Y') = $ANO AND a.IDTurma = $t->IDTurma
            GROUP BY 
                a.id, d.id, m.Nome, t.MediaPeriodo, t.TPAvaliacao, t.MINFrequencia
            SQL;

            $QueryRec = DB::select($SQL);
            $Recuperacoes = [];
            foreach($QueryRec as $r){
                $Frequencia = ($r->FrequenciaAno / 200) * 100 . " %";
                $MediaTotal = $r->MediaPeriodo * 4;       
                if($r->TPAvaliacao == "Nota"){
                    if($r->RecBim > 0){
                        $Total = ($r->RecBim - $r->TotalAno) + $r->PontBim;
                        //dd($Total);
                    }else{
                        $Total = $r->TotalAno;
                    }
                    
                    $Resultado = ($Total > $MediaTotal) ? 'Aprovado' : 'Reprovado';
                }else{
                    $Resultado = "Conceito Sob. Avaliação";
                }

                if (!isset($Recuperacoes[$r->Aluno])) {
                    $Recuperacoes[$r->Aluno] = []; // Inicializa o array para o aluno se não existir
                }

                if($Resultado == "Reprovado"){
                    $Recuperacoes[$r->Aluno][] = $r->Disciplina;
                }
            }

            // Definir fonte para o corpo do relatório
            $pdf->SetFont('Arial', '', 10);
            //CABECALHO DA TABELA
            $pdf->Cell(153,10,self::utfConvert("Turma ".$t->Serie)." ".$t->Turma ." - Média:".$t->MediaPeriodo*4.,1);
            $pdf->Ln();
            $pdf->Cell(13, 8, self::utfConvert('N°'), 1);
            $pdf->Cell(65, 8, self::utfConvert('Nome'), 1);
            $pdf->Cell(75, 8, self::utfConvert('Disciplinas'), 1);
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 8);
            //CORPO DA LISTA    
            $numAluno = 0;
            foreach($Recuperacoes as $key => $r){
                $numAluno++;
                $pdf->Cell(13, 8, self::utfConvert($numAluno), 1);
                $pdf->Cell(65, 8, self::utfConvert($key), 1);
                $pdf->Cell(75, 8, self::utfConvert(implode(',',$r)), 1);
                $pdf->Ln();
            }
            $pdf->Ln(10);
            $pageCount++;
        }

        $pdf->Ln();
        $pdf->Cell(0, 10, self::utfConvert("Emitido Por ".Auth::user()->name." no Dia ".date('d/m/Y H:i')), 0, 1, 'C');
        //DOWNLOAD
        $pdf->Output('I',"Alunos Recuperacao".'.pdf');
        exit;
    }

    public function getRecuperacaoFinalFaltas(){
        $ANO = date('Y');
        $WHERE = "WHERE ";
        if(in_array(Auth::user()->tipo,[2,2.5])){
            $WHERE .= "e.IDOrg=".Auth::user()->id_org;;
        }else{
            $WHERE .="e.id = ".self::getEscolaDiretor(Auth::user()->id);
        }
        $SQLTurmas = "SELECT t.id as IDTurma,t.Nome as Turma,t.Serie,e.Nome as Escola,t.MediaPeriodo FROM turmas t INNER JOIN escolas e ON(e.id = t.IDEscola) $WHERE";

        $Turmas = DB::select($SQLTurmas);

        $pdf = new FPDF();
        $pdf->AddPage();
        // Definir margens
        $pdf->SetMargins(3, 3, 3); // Margens esquerda, superior e direita

        $pdf->SetFont('Arial', 'B', 16);

        $pdf->Cell(0, 10, self::utfConvert("Alunos Reprovados por Faltas"), 0, 1, 'C'); // Nome da escola centralizado
        $pdf->Ln(10);

        $pageCount = 0;
        foreach($Turmas as $t){
            if ($pageCount % 1 == 0 && $pageCount > 0) {
                $pdf->AddPage();
            }

            $SQL = <<<SQL
            SELECT 
                (SELECT SUM(n2.Nota) 
                FROM notas n2 
                INNER JOIN atividades at2 ON n2.IDAtividade = at2.id 
                INNER JOIN aulas au3 ON at2.IDAula = au3.id 
                WHERE au3.IDDisciplina = d.id 
                AND n2.IDAluno = a.id 
                AND DATE_FORMAT(au3.created_at, '%Y') = $ANO
                ) as TotalAno,

                (SELECT SUM(rec2.Nota) 
                FROM recuperacao rec2 
                WHERE rec2.Estagio != 'ANUAL' 
                AND rec2.IDAluno = a.id 
                AND rec2.IDDisciplina = d.id 
                ) as RecBim,

                -- Frequência (quantidade de presenças) do Aluno para a Disciplina e Estágio específicos
                (SELECT COUNT(f2.id) 
                FROM frequencia f2 
                INNER JOIN aulas au2 ON au2.id = f2.IDAula 
                WHERE f2.IDAluno = a.id 
                AND au2.IDDisciplina = d.id 
                AND DATE_FORMAT(au2.created_at, '%Y') = $ANO
                ) as FrequenciaAno,
                (SELECT SUM(rec2.Nota) FROM recuperacao rec2 WHERE rec2.Estagio != 'ANUAL' AND rec2.IDAluno = a.id AND rec2.IDDisciplina = d.id ) as RecBim,
                (SELECT SUM(rec2.PontuacaoPeriodo) FROM recuperacao rec2 WHERE rec2.Estagio != 'ANUAL' AND rec2.IDAluno = a.id AND rec2.IDDisciplina = d.id ) as PontBim,
                -- Caso em que é verificado se a nota é inferior à média
                CASE WHEN 
                    (SELECT SUM(n2.Nota) 
                    FROM notas n2 
                    INNER JOIN atividades at2 ON n2.IDAtividade = at2.id 
                    INNER JOIN aulas au3 ON at2.IDAula = au3.id 
                    WHERE au3.IDDisciplina = 10 
                    AND n2.IDAluno = a.id 
                    AND DATE_FORMAT(n2.created_at, '%Y') = $ANO
                    ) < t.MediaPeriodo THEN 'Reprovado' 
                ELSE 'Aprovado' END as Resultado,
                m.Nome as Aluno,         -- Nome do Aluno
                a.id as IDAluno,         -- ID do Aluno
                d.NMDisciplina as Disciplina, -- Nome da Disciplina
                t.MediaPeriodo,
                t.TPAvaliacao,
                t.MINFrequencia
            FROM 
                alunos a
            INNER JOIN 
                matriculas m ON m.id = a.IDMatricula   -- Relaciona alunos com suas matrículas
            INNER JOIN 
                turmas t ON t.id = a.IDTurma           -- Relaciona alunos com suas turmas
            INNER JOIN 
                aulas au ON t.id = au.IDTurma          -- Relaciona turmas com aulas
            INNER JOIN 
                disciplinas d ON d.id = au.IDDisciplina -- Relaciona aulas com disciplinas
            INNER JOIN 
                notas n ON n.IDAluno = a.id            -- Relaciona notas com alunos
            WHERE 
                DATE_FORMAT(au.created_at, '%Y') = $ANO AND a.IDTurma = $t->IDTurma
            GROUP BY 
                a.id, d.id, m.Nome, t.MediaPeriodo, t.TPAvaliacao, t.MINFrequencia
            SQL;

            $QueryRec = DB::select($SQL);
            $Recuperacoes = [];
            foreach($QueryRec as $r){
                $Frequencia = ($r->FrequenciaAno / 200) * 100 . " %";
                $MediaTotal = $r->MediaPeriodo * 4;       
                if($r->TPAvaliacao == "Nota"){
                    if($r->RecBim > 0){
                        $Total = ($r->RecBim - $r->TotalAno) + $r->PontBim;
                        //dd($Total);
                    }else{
                        $Total = $r->TotalAno;
                    }
                    
                    $Resultado = ($Frequencia >= $r->MINFrequencia) ? 'Aprovado' : 'Reprovado';
                }else{
                    $Resultado = "Conceito Sob. Avaliação";
                }

                if (!isset($Recuperacoes[$r->Aluno])) {
                    $Recuperacoes[$r->Aluno] = []; // Inicializa o array para o aluno se não existir
                }

                if($Resultado == "Reprovado"){
                    $Recuperacoes[$r->Aluno][] = $r->Disciplina;
                }
            }

            // Definir fonte para o corpo do relatório
            $pdf->SetFont('Arial', '', 10);
            //CABECALHO DA TABELA
            $pdf->Cell(153,10,self::utfConvert("Turma ".$t->Serie)." ".$t->Turma ." - Média:".$t->MediaPeriodo*4.,1);
            $pdf->Ln();
            $pdf->Cell(13, 8, self::utfConvert('N°'), 1);
            $pdf->Cell(65, 8, self::utfConvert('Nome'), 1);
            $pdf->Cell(75, 8, self::utfConvert('Disciplinas'), 1);
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 8);
            //CORPO DA LISTA    
            $numAluno = 0;
            foreach($Recuperacoes as $key => $r){
                $numAluno++;
                $pdf->Cell(13, 8, self::utfConvert($numAluno), 1);
                $pdf->Cell(65, 8, self::utfConvert($key), 1);
                $pdf->Cell(75, 8, self::utfConvert(implode(',',$r)), 1);
                $pdf->Ln();
            }
            $pdf->Ln(10);
            $pageCount++;
        }

        $pdf->Ln();
        $pdf->Cell(0, 10, self::utfConvert("Emitido Por ".Auth::user()->name." no Dia ".date('d/m/Y H:i')), 0, 1, 'C');
        //DOWNLOAD
        $pdf->Output('I',"Alunos Recuperacao".'.pdf');
        exit;
    }

    public function dependenciasEscola(){
        $WHERE = "WHERE ";
        if(in_array(Auth::user()->tipo,[2,2.5])){
            $WHERE .= "e.IDOrg=".Auth::user()->id_org;;
        }else{
            $WHERE .="e.id = ".self::getEscolaDiretor(Auth::user()->id);
        }


        $SQL = "SELECT s.NMSala,e.Nome as Escola,s.TPSala,TMSala FROM salas s INNER JOIN escolas e ON(s.IDEscola = e.id) $WHERE";

        $Dependencias = DB::select($SQL);

        // Criar o PDF com FPDF
        $pdf = new FPDF();
        $pdf->AddPage(); // Adiciona uma página
 
        // Definir margens
        $pdf->SetMargins(10, 10, 10);
 
        // Definir cabeçalho do relatório
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, self::utfConvert("Dependências Escolares"), 0, 1, 'C');
        $pdf->Ln(10); // Espaço após o título
 
        // Definir fonte para o corpo do relatório
        $pdf->SetFont('Arial', '', 10);
        //CABECALHO DA TABELA
        $pdf->Cell(70, 8, self::utfConvert('Escola'), 1);
        $pdf->Cell(40, 8, self::utfConvert('Dependência'), 1);
        $pdf->Cell(40, 8, self::utfConvert('Tipo'), 1);
        $pdf->Cell(40, 8, self::utfConvert('Capacidade (M2)'), 1);
        $pdf->Ln();
        //DADOS
        $pdf->SetFont('Arial', '', 8);
        foreach($Dependencias as $d){
            $pdf->Cell(70, 8, self::utfConvert($d->Escola), 1);
            $pdf->Cell(40, 8, self::utfConvert($d->NMSala), 1);
            $pdf->Cell(40, 8, self::utfConvert($d->TPSala), 1);
            $pdf->Cell(40, 8, self::utfConvert($d->TMSala), 1);
            $pdf->Ln();
        }

        $pdf->Ln();
        $pdf->Cell(0, 10, self::utfConvert("Emitido Por ".Auth::user()->name." no Dia ".date('d/m/Y H:i')), 0, 1, 'C');
        //DOWNLOAD
        $pdf->Output('I', 'Dependencias.pdf');
        exit;
    }

    public function getAlunosTurmas(){
        $idorg = Auth::user()->id_org;
        $WHERE = "WHERE ";
        if(in_array(Auth::user()->tipo,[2,2.5])){
            $WHERE .= "e.IDOrg=".Auth::user()->id_org;;
        }else{
            $WHERE .="e.id = ".self::getEscolaDiretor(Auth::user()->id);
        }

        $SQL = "SELECT t.id as IDTurma,t.Nome as Turma,t.Serie,e.Nome as Escola FROM turmas t INNER JOIN escolas e ON(e.id = t.IDEscola) $WHERE";

        $Turmas = DB::select($SQL);

        $pdf = new FPDF();
        $pdf->AddPage();
        // Definir margens
        $pdf->SetMargins(3, 3, 3); // Margens esquerda, superior e direita

        $pdf->SetFont('Arial', 'B', 16);

        $pdf->Cell(0, 10, self::utfConvert("Lista Oficial de Turmas"), 0, 1, 'C'); // Nome da escola centralizado
        $pdf->Ln(10);
        $pageCount = 0;
        foreach($Turmas as $t){
            if ($pageCount % 1 == 0 && $pageCount > 0) {
                $pdf->AddPage();
            }
            $Alunos = "SELECT
                a.id as IDAluno, 
                m.Nome as Nome,
                t.Nome as Turma,
                e.Nome as Escola,
                t.Serie as Serie,
                m.Nascimento as Nascimento,
                a.STAluno,
                m.Foto,
                m.INEP,
                m.Email,
                ats.created_at as DTSituacao,
                m.CPF,
                resp.NMResponsavel,
                r.ANO,
                m.NEE,
                m.Sexo,
                m.created_at,
                resp.CLResponsavel,
                MAX(tr.Aprovado) as Aprovado,
                cal.INIRematricula,
                cal.TERRematricula,
                cal.INIAno,
                cal.TERAno,
                r.ANO
            FROM matriculas m
            INNER JOIN alunos a ON(a.IDMatricula = m.id)
            LEFT JOIN transferencias tr ON(tr.IDAluno = a.id)
            INNER JOIN turmas t ON(a.IDTurma = t.id)
            INNER JOIN renovacoes r ON(r.IDAluno = a.id)
            LEFT JOIN alteracoes_situacao ats ON(ats.IDAluno = a.id)
            INNER JOIN escolas e ON(t.IDEscola = e.id)
            INNER JOIN organizacoes o ON(e.IDOrg = o.id)
            INNER JOIN calendario cal ON(cal.IDOrg = e.IDOrg)
            INNER JOIN responsavel resp ON(a.id = resp.IDAluno)
            WHERE t.id = $t->IDTurma GROUP BY a.id ORDER BY m.Nome ASC 
            ";
            //ADICIONAR PAGINAS
            //$pdf->AddPage();

            // Definir fonte para o corpo do relatório
            $pdf->SetFont('Arial', '', 10);
            //CABECALHO DA TABELA
            $pdf->Cell(205,10,self::utfConvert("Turma ".$t->Serie)." ".$t->Turma,1);
            $pdf->Ln();
            $pdf->Cell(13, 8, self::utfConvert('N°'), 1);
            $pdf->Cell(65, 8, self::utfConvert('Nome'), 1);
            $pdf->Cell(25, 8, self::utfConvert('Entrada'), 1);
            $pdf->Cell(35, 8, self::utfConvert('Matrícula'), 1);
            $pdf->Cell(10, 8, self::utfConvert('Sexo'), 1);
            $pdf->Cell(25, 8, self::utfConvert('Nascimento'), 1);
            $pdf->Cell(12, 8, self::utfConvert('Idade'), 1);
            $pdf->Cell(20, 8, self::utfConvert('Inclusão'), 1);
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 8);
            //CORPO DAS TURMAS
            foreach(DB::select($Alunos) as $num => $al){
                switch($al->STAluno){
                    case "0":
                        $Situacao = 'Frequente';
                        $dataSaida = "";
                        $Vencimento = Carbon::parse($al->INIRematricula);
                        $Hoje = Carbon::parse(date('Y-m-d'));
                        $SitMatricula = $Vencimento->lt($Hoje) && $al->ANO <= date('Y') ? "PENDENTE RENOVAÇÃO" : "RENOVADA";
                        $freq = 1;
                    break;
                    case "1":
                        $Situacao = "Evadido";
                        $dataSaida = "";
                        $freq = 0;
                    break;
                    case "2":
                        $Situacao = "Desistente";
                        $dataSaida = date('d/m/Y',strtotime($al->DTSituacao));
                        $freq = 0;
                    break;
                    case "3":
                        $Situacao = "Desligado";
                        $dataSaida = date('d/m/Y',strtotime($al->DTSituacao));
                        $freq = 0;
                    break;
                    case "4":
                        $Situacao = "Egresso";
                        $dataSaida = date('d/m/Y',strtotime($al->DTSituacao));
                        $freq = 0;
                    break;
                    case "5":
                        $Situacao = "Transferido Para Outra Rede";
                        $dataSaida = "";
                        $freq = 0;
                    break;
                }

                if($freq == 1){
                    $Mat = $Situacao." - ".$SitMatricula;
                }else{
                    $Mat = $Situacao." - ".$dataSaida;
                }

                $pdf->Cell(13, 8, self::utfConvert($num+1), 1);
                $pdf->Cell(65, 8, self::utfConvert($al->Nome), 1);
                $pdf->Cell(25, 8, self::utfConvert(date('d/m/Y',strtotime($al->created_at))), 1);
                $pdf->Cell(35, 8, self::utfConvert($Mat), 1);
                $pdf->Cell(10, 8, self::utfConvert($al->Sexo), 1);
                $pdf->Cell(25, 8, self::utfConvert(date('d/m/Y',strtotime($al->Nascimento))), 1);
                $pdf->Cell(12, 8, self::utfConvert(Carbon::parse($al->Nascimento)->age), 1);
                $pdf->Cell(20, 8, self::utfConvert(($al->NEE == 1) ? 'Sim' : 'Não'), 1);
                $pdf->Ln();
            }
            $pdf->Ln(10);
            $pageCount++;
            //
        }

        $pdf->Ln();
        $pdf->Cell(0, 10, self::utfConvert("Emitido Por ".Auth::user()->name." no Dia ".date('d/m/Y H:i')), 0, 1, 'C');
        //DOWNLOAD
        $pdf->Output('I',"Lista de Turmas".'.pdf');
        exit;
    }

    public function getAlunosTurmasEditavel(){
        $idorg = Auth::user()->id_org;
        $WHERE = "WHERE ";
        if(in_array(Auth::user()->tipo,[2,2.5])){
            $WHERE .= "e.IDOrg=".Auth::user()->id_org;;
        }else{
            $WHERE .="e.id = ".self::getEscolaDiretor(Auth::user()->id);
        }

        $SQL = "SELECT t.id as IDTurma,t.Nome as Turma,t.Serie,e.Nome as Escola FROM turmas t INNER JOIN escolas e ON(e.id = t.IDEscola) $WHERE";

        $Turmas = DB::select($SQL);

        // Cabeçalho do documento
        $pdf = new FPDF();
        $pdf->AddPage('L');
        $pdf->SetMargins(10, 10, 10); // Margens esquerda, superior e direita
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, self::utfConvert("Lista Oficial de Turmas"), 0, 1, 'C'); // Título
        $pdf->Ln(10);
        $pageCount = 0;
        foreach($Turmas as $t){
            $Alunos = "SELECT
                a.id as IDAluno, 
                m.Nome as Nome,
                t.Nome as Turma,
                e.Nome as Escola,
                t.Serie as Serie,
                m.Nascimento as Nascimento,
                a.STAluno,
                m.Foto,
                m.INEP,
                m.Email,
                m.Cor,
                ats.created_at as DTSituacao,
                m.CPF,
                resp.NMResponsavel,
                r.ANO,
                m.NEE,
                m.NIS,
                m.RG,
                m.CPF,
                m.created_at as DTMatricula,
                m.SUS,
                m.Naturalidade,
                m.Sexo,
                m.created_at,
                resp.CLResponsavel,
                MAX(tr.Aprovado) as Aprovado,
                cal.INIRematricula,
                cal.TERRematricula,
                cal.INIAno,
                cal.TERAno,
                r.ANO
            FROM matriculas m
            INNER JOIN alunos a ON(a.IDMatricula = m.id)
            LEFT JOIN transferencias tr ON(tr.IDAluno = a.id)
            INNER JOIN turmas t ON(a.IDTurma = t.id)
            INNER JOIN renovacoes r ON(r.IDAluno = a.id)
            LEFT JOIN alteracoes_situacao ats ON(ats.IDAluno = a.id)
            INNER JOIN escolas e ON(t.IDEscola = e.id)
            INNER JOIN organizacoes o ON(e.IDOrg = o.id)
            INNER JOIN calendario cal ON(cal.IDOrg = e.IDOrg)
            INNER JOIN responsavel resp ON(a.id = resp.IDAluno)
            WHERE t.id = $t->IDTurma GROUP BY a.id ORDER BY m.Nome ASC 
            ";
            //ADICIONAR PAGINAS
            //$pdf->AddPage();

            // Definir fonte para o corpo do relatório
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 8, self::utfConvert("Turma ".$t->Serie." ".$t->Turma), 0, 1);
            $pdf->Ln(5);
        
            // Cabeçalho do Aluno
            //CORPO DAS TURMAS
            // Iteração para cada aluno na turma
            foreach(DB::select($Alunos) as $num => $al) {
                if ($pageCount % 1 == 0 && $pageCount > 0) {
                    $pdf->AddPage('L');
                }
                switch($al->STAluno) {
                    case "0": $Situacao = 'Frequente'; break;
                    case "1": $Situacao = "Evadido"; break;
                    case "2": $Situacao = "Desistente"; break;
                    case "3": $Situacao = "Desligado"; break;
                    case "4": $Situacao = "Egresso"; break;
                    case "5": $Situacao = "Transferido Para Outra Rede"; break;
                }

                // Início do bloco de informações do aluno
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(0, 10, self::utfConvert($al->Nome), 0, 1, 'C'); // Título
                $pdf->SetFont('Arial', '', 12);
                $pdf->Cell(0, 6, self::utfConvert("N°: ".($num+1)), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("B.Fam: ".date('d/m/Y', strtotime($al->created_at))), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Matrícula: ".$Situacao), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Sexo: ".$al->Sexo), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Nascimento: ".date('d/m/Y', strtotime($al->Nascimento))), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Idade: ".Carbon::parse($al->Nascimento)->age), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Inclusão: ".(($al->NEE == 1) ? 'Sim' : 'Não')), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("INEP: ".$al->INEP), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("NIS: ".$al->NIS), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Naturalidade: ".$al->Naturalidade), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Tipo Sanguíneo: "), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Endereço: "), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("RG: ".$al->RG), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("CPF: ".$al->CPF), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("SUS: ".$al->SUS), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Data Matrícula: ".$al->DTMatricula), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Raça: ".$al->Cor), 0, 1);

                // Separação entre alunos
                $pdf->Ln(5);
            }
            $pdf->Ln(10);
            $pageCount++;
            //
        }

        $pdf->Ln();
        $pdf->Cell(0, 10, self::utfConvert("Emitido Por ".Auth::user()->name." no Dia ".date('d/m/Y H:i')), 0, 1, 'C');
        //DOWNLOAD
        $pdf->Output('I',"Lista de Turmas".'.pdf');
        exit;
    }

    public function getBolsaFamilia(){
        $idorg = Auth::user()->id_org;
        $WHERE = "WHERE ";
        if(in_array(Auth::user()->tipo,[2,2.5])){
            $WHERE .= "e.IDOrg=".Auth::user()->id_org;;
        }else{
            $WHERE .="e.id = ".self::getEscolaDiretor(Auth::user()->id);
        }

        $SQL = "SELECT t.id as IDTurma,t.Nome as Turma,t.Serie,e.Nome as Escola FROM turmas t INNER JOIN escolas e ON(e.id = t.IDEscola) $WHERE";

        $Turmas = DB::select($SQL);

        $pdf = new FPDF();
        $pdf->AddPage();
        // Definir margens
        $pdf->SetMargins(3, 3, 3); // Margens esquerda, superior e direita

        $pdf->SetFont('Arial', 'B', 16);

        $pdf->Cell(0, 10, self::utfConvert("Beneficiários do Bolsa Família"), 0, 1, 'C'); // Nome da escola centralizado
        $pdf->Ln(10);
        $pageCount = 0;
        foreach($Turmas as $t){
            if ($pageCount % 1 == 0 && $pageCount > 0) {
                $pdf->AddPage();
            }
            $Alunos = "SELECT
                a.id as IDAluno, 
                m.Nome as Nome,
                t.Nome as Turma,
                e.Nome as Escola,
                t.Serie as Serie,
                m.Nascimento as Nascimento,
                a.STAluno,
                m.Foto,
                m.INEP,
                m.Email,
                ats.created_at as DTSituacao,
                m.CPF,
                resp.NMResponsavel,
                r.ANO,
                m.NEE,
                m.Sexo,
                m.created_at,
                resp.CLResponsavel,
                MAX(tr.Aprovado) as Aprovado,
                cal.INIRematricula,
                cal.TERRematricula,
                cal.INIAno,
                cal.TERAno,
                m.NIS
            FROM matriculas m
            INNER JOIN alunos a ON(a.IDMatricula = m.id)
            LEFT JOIN transferencias tr ON(tr.IDAluno = a.id)
            INNER JOIN turmas t ON(a.IDTurma = t.id)
            INNER JOIN renovacoes r ON(r.IDAluno = a.id)
            LEFT JOIN alteracoes_situacao ats ON(ats.IDAluno = a.id)
            INNER JOIN escolas e ON(t.IDEscola = e.id)
            INNER JOIN organizacoes o ON(e.IDOrg = o.id)
            INNER JOIN calendario cal ON(cal.IDOrg = e.IDOrg)
            INNER JOIN responsavel resp ON(a.id = resp.IDAluno)
            WHERE t.id = $t->IDTurma AND m.BolsaFamilia = 1 GROUP BY a.id ORDER BY m.Nome ASC 
            ";
            //ADICIONAR PAGINAS
            //$pdf->AddPage();

            // Definir fonte para o corpo do relatório
            $pdf->SetFont('Arial', '', 10);
            //CABECALHO DA TABELA
            $pdf->Cell(205,10,self::utfConvert("Turma ".$t->Serie)." ".$t->Turma,1);
            $pdf->Ln();
            $pdf->Cell(13, 8, self::utfConvert('N°'), 1);
            $pdf->Cell(65, 8, self::utfConvert('Nome'), 1);
            $pdf->Cell(25, 8, self::utfConvert('Nascimento'), 1);
            $pdf->Cell(55, 8, self::utfConvert('Responsavel'), 1);
            $pdf->Cell(25, 8, self::utfConvert('INEP'), 1);
            $pdf->Cell(22, 8, self::utfConvert('NIS'), 1);
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 8);
            //CORPO DAS TURMAS
            foreach(DB::select($Alunos) as $num => $al){

                $pdf->Cell(13, 8, self::utfConvert($num+1), 1);
                $pdf->Cell(65, 8, self::utfConvert($al->Nome), 1);
                $pdf->Cell(25, 8, self::utfConvert(date('d/m/Y',strtotime($al->Nascimento))), 1);
                $pdf->Cell(55, 8, self::utfConvert($al->NMResponsavel), 1);
                $pdf->Cell(25, 8, self::utfConvert($al->INEP), 1);
                $pdf->Cell(22, 8, self::utfConvert($al->NIS), 1);
                $pdf->Ln();
            }
            $pdf->Ln(10);
            $pageCount++;
            //
        }

        $pdf->Ln();
        $pdf->Cell(0, 10, self::utfConvert("Emitido Por ".Auth::user()->name." no Dia ".date('d/m/Y H:i')), 0, 1, 'C');
        //DOWNLOAD
        $pdf->Output('I',"Beneficiários Bolsa Família".'.pdf');
        exit;
    }

    public function QTAlunosSexo(){
        $idorg = Auth::user()->id_org;
        $WHERE = "WHERE ";
        if(in_array(Auth::user()->tipo,[2,2.5])){
            $WHERE .= "e.IDOrg=".Auth::user()->id_org;;
        }else{
            $WHERE .="e.id = ".self::getEscolaDiretor(Auth::user()->id);
        }

        $SQL = "SELECT t.id as IDTurma,t.Nome as Turma,t.Serie,e.Nome as Escola FROM turmas t INNER JOIN escolas e ON(e.id = t.IDEscola) $WHERE";

        $Turmas = DB::select($SQL);

        $pdf = new FPDF();
        $pdf->AddPage();
        // Definir margens
        $pdf->SetMargins(40, 40, 40); // Margens esquerda, superior e direita

        $pdf->SetFont('Arial', 'B', 16);

        $pdf->Cell(0, 10, self::utfConvert("Alunos por Sexo"), 0, 1, 'C'); // Nome da escola centralizado
        $pdf->Ln(10);
        $pageCount = 0;
        foreach($Turmas as $t){
            $Alunos = "SELECT
                COUNT(a.id) as Quantidade,
                MAX(m.Sexo) as Sexo
            FROM matriculas m
            INNER JOIN alunos a ON(a.IDMatricula = m.id)
            LEFT JOIN transferencias tr ON(tr.IDAluno = a.id)
            INNER JOIN turmas t ON(a.IDTurma = t.id)
            INNER JOIN renovacoes r ON(r.IDAluno = a.id)
            LEFT JOIN alteracoes_situacao ats ON(ats.IDAluno = a.id)
            INNER JOIN escolas e ON(t.IDEscola = e.id)
            INNER JOIN organizacoes o ON(e.IDOrg = o.id)
            INNER JOIN calendario cal ON(cal.IDOrg = e.IDOrg)
            INNER JOIN responsavel resp ON(a.id = resp.IDAluno)
            WHERE t.id = $t->IDTurma AND a.STAluno = 0 GROUP BY m.Sexo
            ";

            $QTAlunosSQL = "SELECT
                a.id
            FROM matriculas m
            INNER JOIN alunos a ON(a.IDMatricula = m.id)
            LEFT JOIN transferencias tr ON(tr.IDAluno = a.id)
            INNER JOIN turmas t ON(a.IDTurma = t.id)
            INNER JOIN renovacoes r ON(r.IDAluno = a.id)
            LEFT JOIN alteracoes_situacao ats ON(ats.IDAluno = a.id)
            INNER JOIN escolas e ON(t.IDEscola = e.id)
            INNER JOIN organizacoes o ON(e.IDOrg = o.id)
            INNER JOIN calendario cal ON(cal.IDOrg = e.IDOrg)
            INNER JOIN responsavel resp ON(a.id = resp.IDAluno)
            WHERE t.id = $t->IDTurma AND a.STAluno = 0 GROUP BY m.id
            ";

            $QTAlunos = DB::select($QTAlunosSQL);
            //dd(count($QTAlunos));
            //ADICIONAR PAGINAS
            //$pdf->AddPage();

            // Definir fonte para o corpo do relatório
            $pdf->SetFont('Arial', '', 10);
            //CABECALHO DA TABELA
            $pdf->Cell(135,10,self::utfConvert("Turma ".$t->Serie)." ".$t->Turma,1);
            $pdf->Ln();
            $pdf->Cell(30, 8, self::utfConvert('Sexo'), 1);
            $pdf->Cell(75, 8, self::utfConvert('Quantidade'), 1);
            $pdf->Cell(30, 8, self::utfConvert('Porcentagem'), 1);
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 8);
            //CORPO DAS TURMAS
            foreach(DB::select($Alunos) as $num => $al){
                $Porcentagem = ($al->Quantidade/count($QTAlunos)) * 100;
                $pdf->Cell(30, 8, self::utfConvert($al->Sexo), 1);
                $pdf->Cell(75, 8, self::utfConvert($al->Quantidade), 1);
                $pdf->Cell(30, 8, round($Porcentagem), 1);
                $pdf->Ln();
            }
            $pdf->Ln(10);
            $pageCount++;
            //
        }

        $pdf->Ln();
        $pdf->Cell(0, 10, self::utfConvert("Emitido Por ".Auth::user()->name." no Dia ".date('d/m/Y H:i')), 0, 1, 'C');
        //DOWNLOAD
        $pdf->Output('I',"Lista de Turmas".'.pdf');
        exit;
    }

    public function getTransporte(){
        $idorg = Auth::user()->id_org;
        $WHERE = "WHERE ";
        if(in_array(Auth::user()->tipo,[2,2.5])){
            $WHERE .= "e.IDOrg=".Auth::user()->id_org;;
        }else{
            $WHERE .="e.id = ".self::getEscolaDiretor(Auth::user()->id);
        }

        $SQL = "SELECT t.id as IDTurma,t.Nome as Turma,t.Serie,e.Nome as Escola FROM turmas t INNER JOIN escolas e ON(e.id = t.IDEscola) $WHERE";

        $Turmas = DB::select($SQL);

        $pdf = new FPDF();
        $pdf->AddPage();
        // Definir margens
        $pdf->SetMargins(3, 3, 3); // Margens esquerda, superior e direita

        $pdf->SetFont('Arial', 'B', 16);

        $pdf->Cell(0, 10, self::utfConvert("Alunos usuários de Transporte"), 0, 1, 'C'); // Nome da escola centralizado
        $pdf->Ln(10);
        $pageCount = 0;
        foreach($Turmas as $t){
            if ($pageCount % 1 == 0 && $pageCount > 0) {
                $pdf->AddPage();
            }
            $Alunos = "SELECT
                a.id as IDAluno, 
                m.Nome as Nome,
                t.Nome as Turma,
                e.Nome as Escola,
                t.Serie as Serie,
                m.Nascimento as Nascimento,
                a.STAluno,
                m.Foto,
                m.INEP,
                m.Email,
                ats.created_at as DTSituacao,
                m.CPF,
                resp.NMResponsavel,
                r.ANO,
                m.NEE,
                m.Sexo,
                m.created_at,
                resp.CLResponsavel,
                MAX(tr.Aprovado) as Aprovado,
                cal.INIRematricula,
                cal.TERRematricula,
                cal.INIAno,
                cal.TERAno,
                m.UF,
                m.Cidade,
                m.Rua,
                m.Numero,
                m.Bairro,
                m.TPTransporte
            FROM matriculas m
            INNER JOIN alunos a ON(a.IDMatricula = m.id)
            LEFT JOIN transferencias tr ON(tr.IDAluno = a.id)
            INNER JOIN turmas t ON(a.IDTurma = t.id)
            INNER JOIN renovacoes r ON(r.IDAluno = a.id)
            LEFT JOIN alteracoes_situacao ats ON(ats.IDAluno = a.id)
            INNER JOIN escolas e ON(t.IDEscola = e.id)
            INNER JOIN organizacoes o ON(e.IDOrg = o.id)
            INNER JOIN calendario cal ON(cal.IDOrg = e.IDOrg)
            INNER JOIN responsavel resp ON(a.id = resp.IDAluno)
            WHERE t.id = $t->IDTurma AND a.STAluno = 0 AND m.Transporte = 1 GROUP BY a.id ORDER BY m.Nome ASC 
            ";
            //ADICIONAR PAGINAS
            //$pdf->AddPage();

            // Definir fonte para o corpo do relatório
            $pdf->SetFont('Arial', '', 10);
            //CABECALHO DA TABELA
            $pdf->Cell(205,10,self::utfConvert("Turma ".$t->Serie)." ".$t->Turma,1);
            $pdf->Ln();
            $pdf->Cell(65, 8, self::utfConvert('Nome'), 1);
            $pdf->Cell(25, 8, self::utfConvert('Transporte'), 1);
            $pdf->Cell(90, 8, self::utfConvert('Endereço'), 1);
            $pdf->Cell(25, 8, self::utfConvert('Motorista'), 1);
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 8);
            //CORPO DAS TURMAS
            foreach(DB::select($Alunos) as $num => $al){
                $pdf->Cell(65, 8, self::utfConvert($al->Nome), 1);
                $pdf->Cell(25, 8, self::utfConvert($al->TPTransporte), 1);
                $pdf->Cell(90, 8, self::utfConvert($al->Rua.", ".$al->Numero." - ".$al->Bairro." ".$al->Cidade." ".$al->UF), 1);
                $pdf->Cell(25, 8, self::utfConvert("Motorista"), 1);
                $pdf->Ln();
            }
            $pdf->Ln(10);
            $pageCount++;
            //
        }

        $pdf->Ln();
        $pdf->Cell(0, 10, self::utfConvert("Emitido Por ".Auth::user()->name." no Dia ".date('d/m/Y H:i')), 0, 1, 'C');
        //DOWNLOAD
        $pdf->Output('I',"Alunos Usuários de Transporte".'.pdf');
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
