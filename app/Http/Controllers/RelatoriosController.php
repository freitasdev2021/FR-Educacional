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
            case "LivroMatricula":
            return self::getLivroMatricula();
            case "getBoletimInformativo":
            return self::getBoletimInformativo();
            case "getAlunosCenso":
            return self::getAlunosCenso();
            case "mapaNotas":
            return self::mapaNotas();
            case "mediasMinimasNecessarias":
            return self::mediasMinimasNecessarias();
            case "Alunos com Foto":
            return self::getAlunosFoto();
            case "Transferidos":
            return self::getTransferidos();
            case 'Responsaveis':
            return self::Responsaveis();
            case "getHorarios":
            return self::getHorarios();
            case "mapaDesempenhoGeral":
            return self::mapaDesempenhoGeral();
        }
    }

    public function Gerar(Request $request,$Tipo){
        try{

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
                (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.IDTurma = a.IDTurma AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) 
                FROM frequencia f2 
                INNER JOIN aulas au2 ON au2.id = f2.IDAula 
                WHERE au2.TPConteudo = 0 AND f2.IDAluno = a.id 
                AND au2.IDDisciplina = d.id 
                AND DATE_FORMAT(au2.DTAula, '%Y') = $ANO
                ) as FrequenciaAno,
                (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.IDTurma = a.IDTurma AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) as FreqTur,
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
                DATE_FORMAT(au.DTAula, '%Y') = $ANO AND a.IDTurma = $t->IDTurma
            GROUP BY 
                a.id, d.id, m.Nome, t.MediaPeriodo, t.TPAvaliacao, t.MINFrequencia
            SQL;

            $QueryRec = DB::select($SQL);
            $Recuperacoes = [];
            foreach($QueryRec as $r){
                $FrequenciaPorc = ($r->FrequenciaAno / $r->FreqTur) * 100 . " %";
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
                AND DATE_FORMAT(au2.DTAula, '%Y') = $ANO
                ) as FrequenciaAno,
                (SELECT SUM(rec2.Nota) FROM recuperacao rec2 WHERE rec2.Estagio != 'ANUAL' AND rec2.IDAluno = a.id AND rec2.IDDisciplina = d.id ) as RecBim,
                (SELECT SUM(rec2.PontuacaoPeriodo) FROM recuperacao rec2 WHERE rec2.Estagio != 'ANUAL' AND rec2.IDAluno = a.id AND rec2.IDDisciplina = d.id ) as PontBim,
                -- Caso em que é verificado se a nota é inferior à média
                CASE WHEN 
                    (SELECT SUM(n2.Nota) 
                    FROM notas n2 
                    INNER JOIN atividades at2 ON n2.IDAtividade = at2.id 
                    INNER JOIN aulas au3 ON at2.IDAula = au3.id 
                    WHERE n2.IDAluno = a.id 
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
                DATE_FORMAT(au.DTAula, '%Y') = $ANO AND a.IDTurma = $t->IDTurma
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
                (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.IDTurma = a.IDTurma AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) 
                FROM frequencia f2 
                INNER JOIN aulas au2 ON au2.id = f2.IDAula 
                WHERE f2.IDAluno = a.id 
                AND au2.IDDisciplina = d.id 
                AND DATE_FORMAT(au2.DTAula, '%Y') = $ANO
                ) as FrequenciaAno,
                (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.IDTurma = a.IDTurma AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) as FreqDisc,
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
                DATE_FORMAT(au.DTAula, '%Y') = $ANO AND a.IDTurma = $t->IDTurma
            GROUP BY 
                a.id, d.id, m.Nome, t.MediaPeriodo, t.TPAvaliacao, t.MINFrequencia
            SQL;

            $QueryRec = DB::select($SQL);
            $Recuperacoes = [];
            foreach($QueryRec as $r){
                $FrequenciaPorc = ($r->FrequenciaAno / 200) * 100 . " %";
                $Frequencia = 100 - $FrequenciaPorc;
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

    public function getBoletimInformativo(){
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
        //RELATÓRIO QUANTITATIVO POR TURMA
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetMargins(10, 10, 10); // Margens esquerda, superior e direita
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, self::utfConvert("Relatórios por Turma"), 0, 1, 'C'); // Título
        $pdf->Ln(10);
       
        //CONTEUDO DO MESMO 
        foreach($Turmas as $t){
            $Progredidos = 0; //ARMAZENA ALUNOS DE REC
            $Iniciantes = 0;
            $alunosturmasql = <<<SQL
                SELECT
                    a.id as IDAluno
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
            SQL;
            foreach(DB::select($alunosturmasql) as $aluno){
                if(AlunosController::getResultadoAno($aluno->IDAluno,date('Y')) == "Aprovado"){
                    $Progredidos++;
                }
                
                if(AlunosController::getIniciante($aluno->IDAluno) == "Sim"){
                    $Iniciantes++;
                }
            }
           
            // Definir fonte para o corpo do relatório
            $pdf->SetFont('Arial', '', 10);
            //CABECALHO DA TABELA
            $pdf->Cell(180,10,self::utfConvert("Turma ".$t->Serie)." ".$t->Turma,1);
            $pdf->Ln();
            $pdf->Cell(90, 8, self::utfConvert('Sexo'), 1);
            $pdf->Cell(90, 8, self::utfConvert('Quantidade'), 1);
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 8);
            //CORPO DA TABELA
            foreach(self::getRelatoriosQuantitativos($t->IDTurma,"m.Sexo","m.Sexo") as $qt){
                $pdf->Cell(90, 8, self::utfConvert($qt->Sexo), 1);
                $pdf->Cell(90, 8, self::utfConvert($qt->Quantidade), 1);
                $pdf->Ln();
            }
            $pdf->Cell(45, 8, self::utfConvert("Total de Alunos: ".self::getQuantidadeAlunosTurma($t->IDTurma,"AND a.STAluno = 0")), 1);
            $pdf->Cell(45, 8, self::utfConvert("Evadidos: ".self::getQuantidadeAlunosTurma($t->IDTurma,"AND a.STAluno = 1")), 1);
            $pdf->Cell(45, 8, self::utfConvert("Progredidos: ".$Progredidos), 1);
            $pdf->Cell(45, 8, self::utfConvert("Novatos: ".$Iniciantes), 1);
            $pdf->Ln(15);
            //
        }
        //MATRICULAS POR TURMA - LISTA NOMINAL
        $pdf->Cell(0, 10, self::utfConvert("Matrículas por Turma"), 0, 1, 'C'); // Título
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
                r.ANO,
                m.Rua,
                m.Numero,
                m.Cidade,
                m.UF,
                m.Bairro,
                m.PaisJSON,
                m.BolsaFamilia
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

                $Pais = json_decode($al->PaisJSON);

                // Início do bloco de informações do aluno
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(0, 10, self::utfConvert($al->Nome), 0, 1, 'C'); // Título
                $pdf->SetFont('Arial', '', 12);
                $pdf->Cell(0, 6, self::utfConvert("N°: ".($num+1)), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Bolsa Família: "), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Matrícula: ".$Situacao), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Sexo: ".$al->Sexo), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Nascimento: ".date('d/m/Y', strtotime($al->Nascimento))), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Idade: ".Carbon::parse($al->Nascimento)->age), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Inclusão: ".(($al->NEE == 1) ? 'Sim' : 'Não')), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("INEP: ".$al->INEP), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("NIS: ".$al->NIS), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Naturalidade: ".$al->Naturalidade), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Tipo Sanguíneo: O+"), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Endereço: ".$al->Rua.", ".$al->Numero." ".$al->Bairro." ".$al->Cidade." - ".$al->UF), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("RG: ".$al->RG), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("CPF: ".$al->CPF), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("SUS: ".$al->SUS), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Data Matrícula: ".date('d/m/Y',strtotime($al->DTMatricula))), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Raça: ".$al->Cor), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Filiação: "."Teste"." e "."Teste"), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Resultado do Ano Anterior: ".AlunosController::getResultadoAno($al->IDAluno,date('Y')-1)), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("È Iniciante na Unidade?: ".AlunosController::getIniciante($al->IDAluno)), 0, 1);
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

    public function getLivroMatricula(){
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
        $pdf->AddPage();
        $pdf->SetMargins(10, 10, 10); // Margens esquerda, superior e direita
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, self::utfConvert("Livro de Matrícula"), 0, 1, 'C'); // Título
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
                r.ANO,
                m.Rua,
                m.Numero,
                m.Cidade,
                m.UF,
                m.Bairro,
                m.PaisJSON,
                m.BolsaFamilia
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

                $Pais = json_decode($al->PaisJSON);

                // Início do bloco de informações do aluno
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(0, 10, self::utfConvert($al->Nome), 0, 1, 'C'); // Título
                $pdf->SetFont('Arial', '', 12);
                $pdf->Cell(0, 6, self::utfConvert("N°: ".($num+1)), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Bolsa Família: "), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Matrícula: ".$Situacao), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Sexo: ".$al->Sexo), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Nascimento: ".date('d/m/Y', strtotime($al->Nascimento))), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Idade: ".Carbon::parse($al->Nascimento)->age), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Inclusão: ".(($al->NEE == 1) ? 'Sim' : 'Não')), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("INEP: ".$al->INEP), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("NIS: ".$al->NIS), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Naturalidade: ".$al->Naturalidade), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Tipo Sanguíneo: O+"), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Endereço: ".$al->Rua.", ".$al->Numero." ".$al->Bairro." ".$al->Cidade." - ".$al->UF), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("RG: ".$al->RG), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("CPF: ".$al->CPF), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("SUS: ".$al->SUS), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Data Matrícula: ".date('d/m/Y',strtotime($al->DTMatricula))), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Raça: ".$al->Cor), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Filiação: "."Teste"." e "."Teste"), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("Resultado do Ano Anterior: ".AlunosController::getResultadoAno($al->IDAluno,date('Y')-1)), 0, 1);
                $pdf->Cell(0, 6, self::utfConvert("È Iniciante na Unidade?: ".AlunosController::getIniciante($al->IDAluno)), 0, 1);
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

    public function getAlunosCenso(){
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

        $pdf->Cell(0, 10, self::utfConvert("Lista Oficial do Último Censo"), 0, 1, 'C'); // Nome da escola centralizado
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
            WHERE t.id = $t->IDTurma AND DATE(m.created_at) <= DATE_FORMAT(CONCAT(r.ANO, '-05-'), '%Y-%m-%d') GROUP BY a.id ORDER BY m.Nome ASC 
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

    public function mediasMinimasNecessarias()
    {
        $IDOrg = Auth::user()->id_org;
        // Exemplo de dados da consulta SQL que serão usados para gerar o boletim
        $boletins = array();
        $SQLQuery = <<<SQL
            SELECT
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
            WHERE e.IDOrg = $IDOrg GROUP BY a.id ORDER BY m.Nome ASC 
        SQL;
        foreach(DB::select($SQLQuery) as $a){
            $SQL = <<<SQL
            SELECT 
                d.NMDisciplina as Disciplina,
                d.id as IDDisciplina,
                (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.DTAula > a.DTEntrada AND auFreq.IDTurma = t.id AND auFreq.IDDisciplina = d.id AND auFreq.Estagio="1º BIM" AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE TPConteudo = 0 AND f2.IDAluno = $a->IDAluno AND au.id AND au2.IDDisciplina = d.id AND au2.Estagio="1º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y')) as Faltas1B,
                (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.DTAula > a.DTEntrada AND auFreq.IDTurma = t.id AND auFreq.IDDisciplina = d.id AND auFreq.Estagio="2º BIM" AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE TPConteudo = 0 AND f2.IDAluno = $a->IDAluno AND au.id AND au2.IDDisciplina = d.id AND au2.Estagio="2º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') ) as Faltas2B,
                (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.DTAula > a.DTEntrada AND auFreq.IDTurma = t.id AND auFreq.IDDisciplina = d.id AND auFreq.Estagio="3º BIM" AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE TPConteudo = 0 AND f2.IDAluno = $a->IDAluno AND au.id AND au2.IDDisciplina = d.id AND au2.Estagio="3º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') ) as Faltas3B,
                (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.DTAula > a.DTEntrada AND auFreq.IDTurma = t.id AND auFreq.IDDisciplina = d.id AND auFreq.Estagio="4º BIM" AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE TPConteudo = 0 AND f2.IDAluno = $a->IDAluno AND au.id AND au2.IDDisciplina = d.id AND au2.Estagio="4º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') ) as Faltas4B,
                CASE WHEN 
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "1º BIM" AND rec2.IDAluno = $a->IDAluno AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y')) > 0
                THEN
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "1º BIM" AND rec2.IDAluno = $a->IDAluno AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y'))
                ELSE 
                    (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND au3.Estagio='1º BIM' AND DATE_FORMAT(n2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') )
                END as Nota1B,
                CASE WHEN 
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "2º BIM" AND rec2.IDAluno = $a->IDAluno AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y')) > 0
                THEN
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "2º BIM" AND rec2.IDAluno = $a->IDAluno AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y'))
                ELSE 
                    (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND au3.Estagio='2º BIM' AND DATE_FORMAT(n2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') )
                END as Nota2B,
                CASE WHEN 
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "3º BIM" AND rec2.IDAluno = $a->IDAluno AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y')) > 0
                THEN
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "3º BIM" AND rec2.IDAluno = $a->IDAluno AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y'))
                ELSE 
                    (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND au3.Estagio='3º BIM' AND DATE_FORMAT(n2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') )
                END as Nota3B,
                CASE WHEN 
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "4º BIM" AND rec2.IDAluno = $a->IDAluno AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y')) > 0
                THEN
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "4º BIM" AND rec2.IDAluno = $a->IDAluno AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y'))
                ELSE 
                    (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND au3.Estagio='4º BIM' AND DATE_FORMAT(n2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') )
                END as Nota4B
            FROM disciplinas d
            INNER JOIN aulas au ON(d.id = au.IDDisciplina)
            INNER JOIN frequencia f ON(au.id = f.IDAula)
            INNER JOIN alunos a ON(a.id = f.IDAluno)
            INNER JOIN turmas t ON(a.IDTurma = t.id)
            INNER JOIN atividades at ON(at.IDAula = au.id)
            INNER JOIN notas n ON(at.id = n.IDAtividade)
            WHERE a.id = $a->IDAluno
            GROUP BY d.id 
        SQL;
        $queryBoletim = DB::select($SQL);
        
        // Verificar se o aluno tem notas lançadas
        if (!empty($queryBoletim)) {
            $queryAluno = DB::select("SELECT m.Nome as Aluno,t.Nome as Turma,e.Nome as Escola,t.Serie,t.id as IDTurma,t.MediaPeriodo FROM alunos a INNER JOIN matriculas m ON(m.id = a.IDMatricula) INNER JOIN turmas t ON(t.id = a.IDTurma) INNER JOIN escolas e ON(t.IDEscola = e.id) WHERE a.id = $a->IDAluno")[0];
            $boletins[$a->IDAluno] = array(
                "DadosAluno" => $queryAluno,
                "Disciplinas" => []
            );
            // Adicionar ao boletim somente se há dados
            foreach ($queryBoletim as $boletim) {
                $boletins[$a->IDAluno]['Disciplinas'][] = array(
                    "Disciplina" => $boletim->Disciplina,
                    "IDDisciplina" => $boletim->IDDisciplina,
                    "Nota1B"=> $boletim->Nota1B,
                    "Nota2B"=> $boletim->Nota2B,
                    "Nota3B"=> $boletim->Nota3B,
                    "Nota4B"=> $boletim->Nota4B,
                    "Faltas1B"=> $boletim->Faltas1B,
                    "Faltas2B"=> $boletim->Faltas2B,
                    "Faltas3B"=> $boletim->Faltas3B,
                    "Faltas4B"=> $boletim->Faltas4B,
                );
            }
        }
                
        }
        //dd($boletins);
        //RESGATAR DADOS DA ORGANIZACAO
        // Criar o PDF com FPDF
        $pdf = new FPDF();
        $pdf->AddPage(); // Adiciona a página antes do loop
        $pdf->SetFillColor(255, 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        // Definir margens
        $pdf->SetMargins(3, 3, 3); // Define margens: esquerda, superior e direita
        $pdf->SetFont('Arial', 'B', 16);

        $pdf->Cell(0, 10, self::utfConvert("Médias Mínimas e Necessárias"), 0, 1, 'C'); // Nome da escola centralizado
        $pdf->Ln(5);
        // Loop através dos boletins
        foreach ($boletins as $row) {
            // Adiciona uma nova página a cada dois boletins
            $pdf->SetFillColor(255, 0, 0);
            $pdf->SetTextColor(0, 0, 0);
            //CABECALHO DO BOLETIM
            $pdf->SetFont('Arial', 'B', 6);
            $pdf->Ln(3);
            $pdf->Cell(68, 7, mb_convert_encoding("Escola: " . $row['DadosAluno']->Escola, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(68, 7, mb_convert_encoding("Turma: ".$row['DadosAluno']->Serie ." - ".$row['DadosAluno']->Turma, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(68, 7, mb_convert_encoding("Ano Letivo: ". date('Y'), 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Ln();
            $pdf->Cell(204, 7, mb_convert_encoding("Aluno: " . $row['DadosAluno']->Aluno , 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Ln();
            //ETAPAS
            $pdf->SetFillColor(255, 0, 0);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(116, 7, 'Etapas', 1, 0, 'C');
            $pdf->Cell(22, 7, mb_convert_encoding("1º BIM", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(22, 7, mb_convert_encoding("2º BIM", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(22, 7, mb_convert_encoding("3º BIM", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(22, 7, mb_convert_encoding("4º BIM", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Ln();
            $pdf->SetFillColor(255, 0, 0);
            $pdf->SetTextColor(0, 0, 0);
            //NOTAS E FALTAS
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(86, 7, 'Docente', 1, 0, 'C');
            $pdf->Cell(30, 7, 'DISCIPLINA', 1, 0, 'C');
            $pdf->Cell(11, 7, 'Nota', 1, 0, 'C');
            $pdf->Cell(11, 7, 'Falta', 1, 0, 'C');
            $pdf->Cell(11, 7, 'Nota', 1, 0, 'C');
            $pdf->Cell(11, 7, 'Falta', 1, 0, 'C');
            $pdf->Cell(11, 7, 'Nota', 1, 0, 'C');
            $pdf->Cell(11, 7, 'Falta', 1, 0, 'C');
            $pdf->Cell(11, 7, 'Nota', 1, 0, 'C');
            $pdf->Cell(11, 7, 'Falta', 1, 1, 'C');
            //DADOS DO BOLETIM
            foreach($row['Disciplinas'] as $d){
                $pdf->SetFont('Arial', '', 10);
                $pdf->SetFillColor(255, 0, 0);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(86, 7, self::utfConvert(EscolasController::getProfessorDisciplina($d['IDDisciplina'],$row['DadosAluno']->IDTurma)->Professor), 1, 0, 'C');
                $pdf->Cell(30, 7,self::utfConvert($d['Disciplina']), 1, 0, 'C');
                if($row['DadosAluno']->MediaPeriodo < $d['Nota1B']){
                    $pdf->SetFillColor(255, 0, 0);
                    $pdf->SetTextColor(255, 255, 255);
                }else{
                    $pdf->SetTextColor(0, 0, 0);
                }
                $pdf->Cell(11, 7, $d['Nota1B'], 1, 0, 'C',true);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(11, 7, $d['Faltas1B'], 1, 0, 'C');
                if($row['DadosAluno']->MediaPeriodo < $d['Nota2B']){
                    $pdf->SetFillColor(255, 0, 0);
                    $pdf->SetTextColor(255, 255, 255);
                }else{
                    $pdf->SetTextColor(0, 0, 0);
                }
                $pdf->Cell(11, 7, $d['Nota2B'], 1, 0, 'C',true);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(11, 7, $d['Faltas2B'], 1, 0, 'C');
                if($row['DadosAluno']->MediaPeriodo < $d['Nota3B']){
                    $pdf->SetFillColor(255, 0, 0);
                    $pdf->SetTextColor(255, 255, 255);
                }else{
                    $pdf->SetTextColor(0, 0, 0);
                }
                $pdf->Cell(11, 7, $d['Nota3B'], 1, 0, 'C',true);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(11, 7, $d['Faltas3B'], 1, 0, 'C');
                if($row['DadosAluno']->MediaPeriodo < $d['Nota4B']){
                    $pdf->SetFillColor(255, 0, 0);
                    $pdf->SetTextColor(255, 255, 255);
                }else{
                    $pdf->SetTextColor(0, 0, 0);
                }
                $pdf->Cell(11, 7, $d['Nota4B'], 1, 0, 'C',true);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(11, 7, $d['Faltas4B'], 1, 1, 'C');
            }
            //RODAPÉ
            $pdf->Ln(1);
            //CONTADOR DE BOLETINS
        }

        // Retorna o PDF
        $pdf->Output('I', 'mapanotas.pdf');
        exit;
    }


    public function mapaDesempenhoGeral()
    {
        $IDOrg = Auth::user()->id_org;
        // Exemplo de dados da consulta SQL que serão usados para gerar o boletim
        $boletins = array();
        $SQLQuery = <<<SQL
            SELECT
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
            WHERE e.IDOrg = $IDOrg GROUP BY a.id ORDER BY m.Nome ASC 
        SQL;
        foreach(DB::select($SQLQuery) as $a){
            $SQL = <<<SQL
            SELECT 
                d.NMDisciplina as Disciplina,
                d.id as IDDisciplina,
                (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.IDDisciplina = d.id AND auFreq.Estagio="1º BIM" AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) 
                FROM frequencia f2 
                INNER JOIN aulas au2 ON(au2.id = f2.IDAula) 
                WHERE au2.TPConteudo = 0 AND f2.IDAluno = $a->IDAluno AND au2.IDDisciplina = d.id AND au2.Estagio = "1º BIM" 
                AND au2.DTAula > a.DTEntrada AND au2.IDTurma = t.id AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(), '%Y')) as Faltas1B,
            
            (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.IDDisciplina = d.id AND auFreq.Estagio="2º BIM" AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) 
            FROM frequencia f2 
            INNER JOIN aulas au2 ON(au2.id = f2.IDAula) 
            WHERE au2.TPConteudo = 0 AND f2.IDAluno = $a->IDAluno AND au2.IDDisciplina = d.id AND au2.Estagio = "2º BIM" 
            AND au2.DTAula > a.DTEntrada AND au2.IDTurma = t.id AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(), '%Y')) as Faltas2B,
            
            (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.IDDisciplina = d.id AND auFreq.Estagio="3º BIM" AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) 
                FROM frequencia f2 
                INNER JOIN aulas au2 ON(au2.id = f2.IDAula) 
                WHERE au2.TPConteudo = 0 AND f2.IDAluno = $a->IDAluno AND au2.IDDisciplina = d.id AND au2.Estagio = "3º BIM" 
                AND au2.DTAula > a.DTEntrada AND au2.IDTurma = t.id AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(), '%Y')) as Faltas3B,
            
            (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.IDDisciplina = d.id AND auFreq.Estagio="4º BIM" AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) 
                FROM frequencia f2 
                INNER JOIN aulas au2 ON(au2.id = f2.IDAula) 
                WHERE au2.TPConteudo = 0 AND f2.IDAluno = $a->IDAluno AND au2.IDDisciplina = d.id AND au2.Estagio = "4º BIM" 
                AND au2.DTAula > a.DTEntrada AND au2.IDTurma = t.id AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(), '%Y')) as Faltas4B,
            
            -- 1º Bimestre
            CASE WHEN 
                (SELECT rec2.Nota 
                FROM recuperacao rec2 
                WHERE rec2.Estagio = "1º BIM" 
                AND rec2.IDAluno = $a->IDAluno 
                AND rec2.IDDisciplina = d.id 
                AND rec2.created_at = DATE_FORMAT(NOW(), '%Y')) > 0
            THEN 
                (SELECT rec2.Nota 
                FROM recuperacao rec2 
                WHERE rec2.Estagio = "1º BIM" 
                AND rec2.IDAluno = $a->IDAluno 
                AND rec2.IDDisciplina = d.id 
                AND rec2.created_at = DATE_FORMAT(NOW(), '%Y'))
            ELSE 
                (SELECT SUM(n2.Nota) 
                FROM notas n2 
                INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) 
                INNER JOIN aulas au3 ON(at2.IDAula = au3.id) 
                WHERE au3.IDDisciplina = d.id 
                AND n2.IDAluno = a.id 
                AND au3.Estagio = '1º BIM' 
                AND DATE_FORMAT(n2.created_at, '%Y') = DATE_FORMAT(NOW(), '%Y'))
            END as Nota1B,

            -- 2º Bimestre
            CASE WHEN 
                (SELECT rec2.Nota 
                FROM recuperacao rec2 
                WHERE rec2.Estagio = "2º BIM" 
                AND rec2.IDAluno = $a->IDAluno 
                AND rec2.IDDisciplina = d.id 
                AND rec2.created_at = DATE_FORMAT(NOW(), '%Y')) > 0
            THEN 
                (SELECT rec2.Nota 
                FROM recuperacao rec2 
                WHERE rec2.Estagio = "2º BIM" 
                AND rec2.IDAluno = $a->IDAluno 
                AND rec2.IDDisciplina = d.id 
                AND rec2.created_at = DATE_FORMAT(NOW(), '%Y'))
            ELSE 
                (SELECT SUM(n2.Nota) 
                FROM notas n2 
                INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) 
                INNER JOIN aulas au3 ON(at2.IDAula = au3.id) 
                WHERE au3.IDDisciplina = d.id 
                AND n2.IDAluno = a.id 
                AND au3.Estagio = '2º BIM' 
                AND DATE_FORMAT(n2.created_at, '%Y') = DATE_FORMAT(NOW(), '%Y'))
            END as Nota2B,

            -- 3º Bimestre
            CASE WHEN 
                (SELECT rec2.Nota 
                FROM recuperacao rec2 
                WHERE rec2.Estagio = "3º BIM" 
                AND rec2.IDAluno = $a->IDAluno 
                AND rec2.IDDisciplina = d.id 
                AND rec2.created_at = DATE_FORMAT(NOW(), '%Y')) > 0
            THEN 
                (SELECT rec2.Nota 
                FROM recuperacao rec2 
                WHERE rec2.Estagio = "3º BIM" 
                AND rec2.IDAluno = $a->IDAluno 
                AND rec2.IDDisciplina = d.id 
                AND rec2.created_at = DATE_FORMAT(NOW(), '%Y'))
            ELSE 
                (SELECT SUM(n2.Nota) 
                FROM notas n2 
                INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) 
                INNER JOIN aulas au3 ON(at2.IDAula = au3.id) 
                WHERE au3.IDDisciplina = d.id 
                AND n2.IDAluno = a.id 
                AND au3.Estagio = '3º BIM' 
                AND DATE_FORMAT(n2.created_at, '%Y') = DATE_FORMAT(NOW(), '%Y'))
            END as Nota3B,

            -- 4º Bimestre
            CASE WHEN 
                (SELECT rec2.Nota 
                FROM recuperacao rec2 
                WHERE rec2.Estagio = "4º BIM" 
                AND rec2.IDAluno = $a->IDAluno 
                AND rec2.IDDisciplina = d.id 
                AND rec2.created_at = DATE_FORMAT(NOW(), '%Y')) > 0
            THEN 
                (SELECT rec2.Nota 
                FROM recuperacao rec2 
                WHERE rec2.Estagio = "4º BIM" 
                AND rec2.IDAluno = $a->IDAluno 
                AND rec2.IDDisciplina = d.id 
                AND rec2.created_at = DATE_FORMAT(NOW(), '%Y'))
            ELSE 
                (SELECT SUM(n2.Nota) 
                FROM notas n2 
                INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) 
                INNER JOIN aulas au3 ON(at2.IDAula = au3.id) 
                WHERE au3.IDDisciplina = d.id 
                AND n2.IDAluno = a.id 
                AND au3.Estagio = '4º BIM' 
                AND DATE_FORMAT(n2.created_at, '%Y') = DATE_FORMAT(NOW(), '%Y'))
            END as Nota4B
            FROM disciplinas d
            INNER JOIN aulas au ON(d.id = au.IDDisciplina)
            INNER JOIN frequencia f ON(au.id = f.IDAula)
            INNER JOIN alunos a ON(a.id = f.IDAluno)
            INNER JOIN turmas t ON(t.id = a.IDTurma)
            INNER JOIN atividades at ON(at.IDAula = au.id)
            INNER JOIN notas n ON(at.id = n.IDAtividade)
            WHERE a.id = $a->IDAluno
            GROUP BY d.id

            SQL;
            
        // echo $SQL;
        
        $queryBoletim = DB::select($SQL);
    
        // Verificar se o aluno tem notas lançadas
        if (!empty($queryBoletim)) {
            $queryAluno = DB::select("SELECT m.Nome as Aluno,t.Nome as Turma,e.Nome as Escola,t.Serie,t.id as IDTurma,t.MediaPeriodo FROM alunos a INNER JOIN matriculas m ON(m.id = a.IDMatricula) INNER JOIN turmas t ON(t.id = a.IDTurma) INNER JOIN escolas e ON(t.IDEscola = e.id) WHERE a.id = $a->IDAluno")[0];
            $boletins[$a->IDAluno] = array(
                "DadosAluno" => $queryAluno,
                "Disciplinas" => []
            );
            // Adicionar ao boletim somente se há dados
            foreach ($queryBoletim as $boletim) {
                $boletins[$a->IDAluno]['Disciplinas'][] = array(
                    "Disciplina" => $boletim->Disciplina,
                    "IDDisciplina" => $boletim->IDDisciplina,
                    "IDAluno" => $a->IDAluno,
                    "Nota1B"=> $boletim->Nota1B,
                    "Nota2B"=> $boletim->Nota2B,
                    "Nota3B"=> $boletim->Nota3B,
                    "Nota4B"=> $boletim->Nota4B,
                    "Faltas1B"=> $boletim->Faltas1B,
                    "Faltas2B"=> $boletim->Faltas2B,
                    "Faltas3B"=> $boletim->Faltas3B,
                    "Faltas4B"=> $boletim->Faltas4B,
                );
            }
        }
                
        }

        //dd($boletins);
        //RESGATAR DADOS DA ORGANIZACAO
        // Criar o PDF com FPDF
        $pdf = new FPDF();
        $pdf->AddPage("L"); // Adiciona a página antes do loop
        $pdf->SetFillColor(255, 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        // Definir margens
        $pdf->SetMargins(10, 10, 10); // Define margens: esquerda, superior e direita
        $pdf->SetFont('Arial', 'B', 16);

        $pdf->Cell(0, 10, self::utfConvert("Desempenho Geral"), 0, 1, 'C'); // Nome da escola centralizado
        $pdf->Ln(5);
        // Loop através dos boletins
        foreach ($boletins as $row) {
            // Adiciona uma nova página a cada dois boletins
            $pdf->SetFillColor(255, 0, 0);
            $pdf->SetTextColor(0, 0, 0);
            //CABECALHO DO BOLETIM
            $pdf->SetFont('Arial', 'B', 6);
            $pdf->Ln(3);
            $pdf->Cell(94, 7, mb_convert_encoding("Escola: " . $row['DadosAluno']->Escola, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(94, 7, mb_convert_encoding("Turma: ".$row['DadosAluno']->Serie ." - ".$row['DadosAluno']->Turma, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(93, 7, mb_convert_encoding("Ano Letivo: ". date('Y'), 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Ln();
            $pdf->Cell(281, 7, mb_convert_encoding("Aluno: " . $row['DadosAluno']->Aluno , 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Ln();
            //ETAPAS
            $pdf->SetFillColor(255, 0, 0);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(116, 7, 'Etapas', 1, 0, 'C');
            $pdf->Cell(33, 7, mb_convert_encoding("1º BIM", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(33, 7, mb_convert_encoding("2º BIM", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(33, 7, mb_convert_encoding("3º BIM", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(33, 7, mb_convert_encoding("4º BIM", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(33, 7, mb_convert_encoding("Total", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Ln();
            $pdf->SetFillColor(255, 0, 0);
            $pdf->SetTextColor(0, 0, 0);
            //NOTAS E FALTAS
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(86, 7, 'Docente', 1, 0, 'C');
            $pdf->Cell(30, 7, 'DISCIPLINA', 1, 0, 'C');

            $pdf->Cell(11, 7, 'Nota', 1, 0, 'C');
            $pdf->Cell(11, 7, 'Falta', 1, 0, 'C');
            $pdf->Cell(11, 7, 'Freq', 1, 0, 'C');

            $pdf->Cell(11, 7, 'Nota', 1, 0, 'C');
            $pdf->Cell(11, 7, 'Falta', 1, 0, 'C');
            $pdf->Cell(11, 7, 'Freq', 1, 0, 'C');

            $pdf->Cell(11, 7, 'Nota', 1, 0, 'C');
            $pdf->Cell(11, 7, 'Falta', 1, 0, 'C');
            $pdf->Cell(11, 7, 'Freq', 1, 0, 'C');

            $pdf->Cell(11, 7, 'Nota', 1, 0, 'C');
            $pdf->Cell(11, 7, 'Falta', 1, 0, 'C');
            $pdf->Cell(11, 7, 'Freq', 1, 0, 'C');

            $pdf->Cell(11, 7, 'Nota', 1, 0, '');
            $pdf->Cell(11, 7, 'Falta', 1, 0, '');
            $pdf->Cell(11, 7, 'Freq', 1, 1, '');
            //DADOS DO BOLETIM
            foreach($row['Disciplinas'] as $d){
                $pdf->SetFont('Arial', '', 10);
                $pdf->SetFillColor(255, 0, 0);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(86, 7, self::utfConvert(EscolasController::getProfessorDisciplina($d['IDDisciplina'],$row['DadosAluno']->IDTurma)->Professor), 1, 0, 'C');
                $pdf->Cell(30, 7,self::utfConvert($d['Disciplina']), 1, 0, 'C');
                if($row['DadosAluno']->MediaPeriodo < $d['Nota1B']){
                    $pdf->SetFillColor(255, 0, 0);
                    $pdf->SetTextColor(255, 255, 255);
                }else{
                    $pdf->SetTextColor(0, 0, 0);
                }
                $pdf->Cell(11, 7, $d['Nota1B'], 1, 0, 'C',true);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(11, 7, $d['Faltas1B'], 1, 0, 'C');
                $pdf->Cell(11, 7, ($d['Faltas1B']/50)*100, 1, 0, 'C');
                /////
                if($row['DadosAluno']->MediaPeriodo < $d['Nota2B']){
                    $pdf->SetFillColor(255, 0, 0);
                    $pdf->SetTextColor(255, 255, 255);
                }else{
                    $pdf->SetTextColor(0, 0, 0);
                }
                $pdf->Cell(11, 7, $d['Nota2B'], 1, 0, 'C',true);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(11, 7, $d['Faltas2B'], 1, 0, 'C');
                $pdf->Cell(11, 7, ($d['Faltas2B']/50)*100, 1, 0, 'C');
                ////
                if($row['DadosAluno']->MediaPeriodo < $d['Nota3B']){
                    $pdf->SetFillColor(255, 0, 0);
                    $pdf->SetTextColor(255, 255, 255);
                }else{
                    $pdf->SetTextColor(0, 0, 0);
                }
                $pdf->Cell(11, 7, $d['Nota3B'], 1, 0, 'C',true);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(11, 7, $d['Faltas3B'], 1, 0, 'C');
                $pdf->Cell(11, 7, ($d['Faltas3B']/50)*100, 1, 0, 'C');
                ////
                if($row['DadosAluno']->MediaPeriodo < $d['Nota4B']){
                    $pdf->SetFillColor(255, 0, 0);
                    $pdf->SetTextColor(255, 255, 255);
                }else{
                    $pdf->SetTextColor(0, 0, 0);
                }
                $pdf->Cell(11, 7, $d['Nota4B'], 1, 0, 'C',true);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(11, 7, $d['Faltas4B'], 1, 0, 'C');
                $pdf->Cell(11, 7, ($d['Faltas4B']/50)*100, 1, 0, 'C');
                ////
                $Total = AlunosController::getTotalDisciplinaAno($d['IDAluno'],$d['IDDisciplina']);
                if($row['DadosAluno']->MediaPeriodo*4 < $Total){
                    $pdf->SetFillColor(255, 0, 0);
                    $pdf->SetTextColor(255, 255, 255);
                }else{
                    $pdf->SetTextColor(0, 0, 0);
                }
                $faltasGeral = $d['Faltas1B'] + $d['Faltas2B'] + $d['Faltas3B'] + $d['Faltas4B'];
                
                $pdf->Cell(11, 7, $Total, 1, 0, 'C',true);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(11, 7, $faltasGeral, 1, 0, 'C');
                $pdf->Cell(11, 7, ($faltasGeral/200)*100, 1, 1, 'C');
                ////
            }
            //RODAPÉ
            $pdf->Ln(10);
            //CONTADOR DE BOLETINS
        }

        // Retorna o PDF
        $pdf->Output('I', 'mapanotas.pdf');
        exit;
    }

    public function mapaNotas()
    {
        $IDOrg = Auth::user()->id_org;
        // Exemplo de dados da consulta SQL que serão usados para gerar o boletim
        $boletins = array();
        $SQLQuery = <<<SQL
            SELECT
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
            WHERE e.IDOrg = $IDOrg GROUP BY a.id ORDER BY m.Nome ASC 
        SQL;
        foreach(DB::select($SQLQuery) as $a){
            $SQL = <<<SQL
            SELECT 
                d.NMDisciplina as Disciplina,
                (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.DTAula > a.DTEntrada AND auFreq.IDTurma = t.id AND auFreq.IDDisciplina = d.id AND auFreq.Estagio="1º BIM" AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE TPConteudo = 0 AND f2.IDAluno = $a->IDAluno AND au.id AND au2.IDDisciplina = d.id AND au2.Estagio="1º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y')) as Faltas1B,
                (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.DTAula > a.DTEntrada AND auFreq.IDTurma = t.id AND auFreq.IDDisciplina = d.id AND auFreq.Estagio="2º BIM" AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE TPConteudo = 0 AND f2.IDAluno = $a->IDAluno AND au.id AND au2.IDDisciplina = d.id AND au2.Estagio="2º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') ) as Faltas2B,
                (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.DTAula > a.DTEntrada AND auFreq.IDTurma = t.id AND auFreq.IDDisciplina = d.id AND auFreq.Estagio="3º BIM" AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE TPConteudo = 0 AND f2.IDAluno = $a->IDAluno AND au.id AND au2.IDDisciplina = d.id AND au2.Estagio="3º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') ) as Faltas3B,
                (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.DTAula > a.DTEntrada AND auFreq.IDTurma = t.id AND auFreq.IDDisciplina = d.id AND auFreq.Estagio="4º BIM" AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE TPConteudo = 0 AND f2.IDAluno = $a->IDAluno AND au.id AND au2.IDDisciplina = d.id AND au2.Estagio="4º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') ) as Faltas4B,
                CASE WHEN 
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "1º BIM" AND rec2.IDAluno = $a->IDAluno AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y')) > 0
                THEN
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "1º BIM" AND rec2.IDAluno = $a->IDAluno AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y'))
                ELSE 
                    (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND au3.Estagio='1º BIM' AND DATE_FORMAT(n2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') )
                END as Nota1B,
                CASE WHEN 
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "2º BIM" AND rec2.IDAluno = $a->IDAluno AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y')) > 0
                THEN
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "2º BIM" AND rec2.IDAluno = $a->IDAluno AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y'))
                ELSE 
                    (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND au3.Estagio='2º BIM' AND DATE_FORMAT(n2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') )
                END as Nota2B,
                CASE WHEN 
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "3º BIM" AND rec2.IDAluno = $a->IDAluno AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y')) > 0
                THEN
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "3º BIM" AND rec2.IDAluno = $a->IDAluno AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y'))
                ELSE 
                    (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND au3.Estagio='3º BIM' AND DATE_FORMAT(n2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') )
                END as Nota3B,
                CASE WHEN 
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "4º BIM" AND rec2.IDAluno = $a->IDAluno AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y')) > 0
                THEN
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "4º BIM" AND rec2.IDAluno = $a->IDAluno AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y'))
                ELSE 
                    (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND au3.Estagio='4º BIM' AND DATE_FORMAT(n2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') )
                END as Nota4B
            FROM disciplinas d
            INNER JOIN aulas au ON(d.id = au.IDDisciplina)
            INNER JOIN frequencia f ON(au.id = f.IDAula)
            INNER JOIN alunos a ON(a.id = f.IDAluno)
            INNER JOIN turmas t ON(t.id = a.IDTurma)
            INNER JOIN atividades at ON(at.IDAula = au.id)
            INNER JOIN notas n ON(at.id = n.IDAtividade)
            WHERE a.id = $a->IDAluno
            GROUP BY d.id 
        SQL;
        $queryBoletim = DB::select($SQL);
        
        // Verificar se o aluno tem notas lançadas
        if (!empty($queryBoletim)) {
            $queryAluno = DB::select("SELECT m.Nome as Aluno,t.Nome as Turma,e.Nome as Escola,t.Serie FROM alunos a INNER JOIN matriculas m ON(m.id = a.IDMatricula) INNER JOIN turmas t ON(t.id = a.IDTurma) INNER JOIN escolas e ON(t.IDEscola = e.id) WHERE a.id = $a->IDAluno")[0];
            $boletins[$a->IDAluno] = array(
                "DadosAluno" => $queryAluno,
                "Disciplinas" => []
            );
            // Adicionar ao boletim somente se há dados
            foreach ($queryBoletim as $boletim) {
                $boletins[$a->IDAluno]['Disciplinas'][] = array(
                    "Disciplina" => $boletim->Disciplina,
                    "Nota1B"=> $boletim->Nota1B,
                    "Nota2B"=> $boletim->Nota2B,
                    "Nota3B"=> $boletim->Nota3B,
                    "Nota4B"=> $boletim->Nota4B,
                    "Faltas1B"=> $boletim->Faltas1B,
                    "Faltas2B"=> $boletim->Faltas2B,
                    "Faltas3B"=> $boletim->Faltas3B,
                    "Faltas4B"=> $boletim->Faltas4B,
                );
            }
        }
                
        }
        //dd($boletins);
        //RESGATAR DADOS DA ORGANIZACAO
        // Criar o PDF com FPDF
        $pdf = new FPDF();
        $pdf->AddPage(); // Adiciona a página antes do loop

        // Definir margens
        $pdf->SetMargins(5, 5, 5); // Define margens: esquerda, superior e direita
        $pdf->SetFont('Arial', 'B', 16);

        $pdf->Cell(0, 10, self::utfConvert("Mapa de Notas e Faltas"), 0, 1, 'C'); // Nome da escola centralizado
        $pdf->Ln(5);
        // Loop através dos boletins
        foreach ($boletins as $row) {
            // Adiciona uma nova página a cada dois boletins

            //CABECALHO DO BOLETIM
            $pdf->SetFont('Arial', 'B', 6);
            $pdf->Ln(3);
            $pdf->Cell(66, 7, mb_convert_encoding("Escola: " . $row['DadosAluno']->Escola, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(66, 7, mb_convert_encoding("Turma: ".$row['DadosAluno']->Serie ." - ".$row['DadosAluno']->Turma, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(66, 7, mb_convert_encoding("Ano Letivo: ". date('Y'), 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Ln();
            $pdf->Cell(198, 7, mb_convert_encoding("Aluno: " . $row['DadosAluno']->Aluno , 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Ln();
            //ETAPAS
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(30, 7, 'Etapas', 1, 0, 'C');
            $pdf->Cell(42, 7, mb_convert_encoding("1º BIM", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(42, 7, mb_convert_encoding("2º BIM", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(42, 7, mb_convert_encoding("3º BIM", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(42, 7, mb_convert_encoding("4º BIM", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Ln();
            //NOTAS E FALTAS
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(30, 7, 'DISCIPLINAS', 1, 0, 'C');
            $pdf->Cell(21, 7, 'Nota', 1, 0, 'C');
            $pdf->Cell(21, 7, 'Falta', 1, 0, 'C');
            $pdf->Cell(21, 7, 'Nota', 1, 0, 'C');
            $pdf->Cell(21, 7, 'Falta', 1, 0, 'C');
            $pdf->Cell(21, 7, 'Nota', 1, 0, 'C');
            $pdf->Cell(21, 7, 'Falta', 1, 0, 'C');
            $pdf->Cell(21, 7, 'Nota', 1, 0, 'C');
            $pdf->Cell(21, 7, 'Falta', 1, 1, 'C');
            //DADOS DO BOLETIM
            foreach($row['Disciplinas'] as $d){
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(30, 7, mb_convert_encoding($d['Disciplina'],'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
                $pdf->Cell(21, 7, $d['Nota1B'], 1, 0, 'C');
                $pdf->Cell(21, 7, $d['Faltas1B'], 1, 0, 'C');
                $pdf->Cell(21, 7, $d['Nota2B'], 1, 0, 'C');
                $pdf->Cell(21, 7, $d['Faltas2B'], 1, 0, 'C');
                $pdf->Cell(21, 7, $d['Nota3B'], 1, 0, 'C');
                $pdf->Cell(21, 7, $d['Faltas3B'], 1, 0, 'C');
                $pdf->Cell(21, 7, $d['Nota4B'], 1, 0, 'C');
                $pdf->Cell(21, 7, $d['Faltas4B'], 1, 1, 'C');
            }
            //RODAPÉ
            $pdf->Ln(1);
            //CONTADOR DE BOLETINS
        }

        // Retorna o PDF
        $pdf->Output('I', 'mapanotas.pdf');
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

    public function getAlunosFoto(){
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
        $pdf->AddPage();
        $pdf->SetMargins(10, 10, 10); // Margens esquerda, superior e direita
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, self::utfConvert("Alunos com Foto"), 0, 1, 'C'); // Título
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
                r.ANO,
                m.CDPasta
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
            if(count(DB::select($Alunos)) > 0){
                foreach(DB::select($Alunos) as $num => $al) {
                    if($al->Foto){
                        if ($pageCount % 1 == 0 && $pageCount > 0) {
                            $pdf->AddPage();
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
                        // Inserir a logo da escola (ajuste o caminho e dimensões da imagem conforme necessário)
                        $pdf->Image(public_path('storage/organizacao_' . Auth::user()->id_org . '_alunos/aluno_' . $al->CDPasta . '/' . $al->Foto), 10, 10, 30); // Caminho da logo, posição X, Y e tamanho
                        // Definir fonte e título
                        $pdf->Ln(40);
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
                }
            }else{
                $pdf->Cell(0, 6, self::utfConvert("Sem Dados"), 0, 1);
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

    public static function getRelatoriosQuantitativos($IDTurma,$GROUPBY,$SELECT){
            $Alunos = "SELECT
                COUNT(a.id) as Quantidade,
                $SELECT
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
            WHERE t.id = $IDTurma AND a.STAluno = 0 GROUP BY $GROUPBY
        ";

        return DB::select($Alunos);
    }

    public static function getQuantidadeAlunosTurma($IDTurma,$WHERE){
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
        WHERE t.id = $IDTurma $WHERE GROUP BY m.id
        ";

        $QTAlunos = count(DB::select($QTAlunosSQL));
        return $QTAlunos;
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

    public function getTransferidos(){
        $AND = " AND eDestino.id IN(".implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)).")";
        $AND .= " OR eOrigem.id IN(".implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)).")";
        $idorg = Auth::user()->id_org;
        $SQL = "SELECT
            a.id as IDAluno, 
            m.Nome as Aluno,
            eDestino.Nome as EscolaDestino,
            eOrigem.Nome as EscolaOrigem,
            tr.Justificativa,
            tr.created_at as DTTransferencia
        FROM transferencias tr
        INNER JOIN alunos a ON(a.id = tr.IDAluno)
        INNER JOIN matriculas m ON(m.id = a.IDMatricula)
        INNER JOIN turmas t ON(a.IDTurma = t.id)
        INNER JOIN escolas eDestino ON(tr.IDEscolaDestino = eDestino.id)
        INNER JOIN escolas eOrigem ON(tr.IDEscolaOrigem = eOrigem.id)
        INNER JOIN organizacoes o ON(eOrigem.IDOrg = o.id)
        WHERE o.id = $idorg $AND AND tr.Aprovado = 1  
        ";
        $Query = DB::select($SQL);

        $pdf = new FPDF();
        $pdf->AddPage();
        // Definir margens
        $pdf->SetMargins(3, 3, 3); // Margens esquerda, superior e direita
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, self::utfConvert("Alunos Transferidos"), 0, 1, 'C'); // Nome da escola centralizado
        $pdf->Ln(10);
        //CABECALHO DA TABELA
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(15, 8, self::utfConvert('N°'), 1);
        $pdf->Cell(45, 8, self::utfConvert('Nome'), 1);
        $pdf->Cell(45, 8, self::utfConvert('Escola de Origem'), 1);
        $pdf->Cell(45, 8, self::utfConvert('Escola de Destino'), 1);
        $pdf->Cell(35, 8, self::utfConvert('Justificativa'), 1);
        $pdf->Cell(20, 8, self::utfConvert('Data'), 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 6);
        //CORPO DA TABELA
        foreach($Query as $t){
            $pdf->Cell(15, 8, self::utfConvert($t->IDAluno), 1);
            $pdf->Cell(45, 8, self::utfConvert($t->Aluno), 1);
            $pdf->Cell(45, 8, self::utfConvert($t->EscolaOrigem), 1);
            $pdf->Cell(45, 8, self::utfConvert($t->EscolaDestino), 1);
            $pdf->Cell(35, 8, self::utfConvert($t->Justificativa), 1);
            $pdf->Cell(20, 8, self::utfConvert(date('d/m/Y',strtotime($t->DTTransferencia))), 1);
            $pdf->Ln();
        }
        //DOWNLOAD
        $pdf->Output('I',"Alunos Recuperacao".'.pdf');
        exit;
    }

    public function getHorarios(){
        $SQL = <<<SQL
        SELECT 
            tn.DiaSemana,
            CONCAT(
                '[',
                GROUP_CONCAT(
                    DISTINCT
                    '{'
                    ,'"Docente":"', p.Nome, '"'
                    ,',"Inicio":"', tn.INITur, '"'
                    ,',"Termino":"', tn.TERTur, '"'
                    ,',"Disciplina":"', d.NMDisciplina, '"'
                    ,',"Escola":"', e.Nome, '"'
                    ,'}'
                    SEPARATOR ','
                ),
                ']'
            ) AS Horarios
        FROM turnos tn
        INNER JOIN turmas t ON tn.IDTurma = t.id
        INNER JOIN alocacoes al ON t.IDEscola = al.IDEscola
        INNER JOIN escolas e ON al.IDEscola = e.id
        INNER JOIN professores p ON p.id = tn.IDProfessor
        INNER JOIN users us ON us.IDProfissional = p.id
        INNER JOIN disciplinas d ON d.id = tn.IDDisciplina
        WHERE al.TPProfissional = 'PROF' 
        GROUP BY tn.DiaSemana;
        SQL;

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetMargins(5, 5,5); // Margens esquerda, superior e direita
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, self::utfConvert("Quadro de Horários"), 0, 1, 'C'); // Título
        $pdf->Ln(10);
        DB::statement("SET SESSION group_concat_max_len = 1000000");
        $Query = DB::select($SQL);
        foreach($Query as $q){
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(200, 7, self::utfConvert($q->DiaSemana), 1, 0, 'C');
            $pdf->Ln();
            //NOTAS E FALTAS
            $pdf->SetFont('Arial', '', 7);
            $pdf->Cell(65, 7, 'Docente', 1, 0, 'C');
            $pdf->Cell(45, 7, 'Disciplina', 1, 0, 'C');
            $pdf->Cell(21, 7, self::utfConvert('Início'), 1, 0, 'C');
            $pdf->Cell(21, 7, self::utfConvert('Término'), 1, 0, 'C');
            $pdf->Cell(48, 7, 'Escola', 1, 0, 'C');
            $pdf->Ln();
            //dd($q->Horarios);
            foreach(json_decode($q->Horarios) as $h){
                $pdf->Cell(65, 7, self::utfConvert($h->Docente), 1, 0, 'C');
                $pdf->Cell(45, 7, self::utfConvert($h->Disciplina), 1, 0, 'C');
                $pdf->Cell(21, 7, self::utfConvert($h->Inicio), 1, 0, 'C');
                $pdf->Cell(21, 7, self::utfConvert($h->Termino), 1, 0, 'C');
                $pdf->Cell(48, 7, self::utfConvert($h->Escola), 1, 0, 'C');
                $pdf->Ln();
            }
            $pdf->Ln();
        }

        $pdf->Cell(0, 10, self::utfConvert("Emitido Por ".Auth::user()->name." no Dia ".date('d/m/Y H:i')), 0, 1, 'C');

        $pdf->Output('I',"Quadro de Horarios".'.pdf');
        exit;
    }

    public function Responsaveis(){
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

        $pdf->Cell(0, 10, self::utfConvert("Lista de Pais"), 0, 1, 'C'); // Nome da escola centralizado
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
                m.PaisJSON,
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
            $pdf->Cell(65, 8, self::utfConvert('Aluno'), 1);
            $pdf->Cell(70, 8, self::utfConvert('Mãe'), 1);
            $pdf->Cell(70, 8, self::utfConvert('Pai'), 1);
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 8);
            //CORPO DAS TURMAS
            foreach(DB::select($Alunos) as $al){
                $Pais = json_decode($al->PaisJSON);
                $pdf->Cell(65, 8, self::utfConvert($al->Nome), 1);
                $pdf->Cell(70, 8, self::utfConvert(!(empty($Pais->Mae)) ? $Pais->Mae : ''), 1);
                $pdf->Cell(70, 8, self::utfConvert(!(empty($Pais->Pai)) ? $Pais->Pai : ''), 1);
                $pdf->Ln();
            }
            $pdf->Ln(10);
            $pageCount++;
            //
        }

        $pdf->Ln();
        $pdf->Cell(0, 10, self::utfConvert("Emitido Por ".Auth::user()->name." no Dia ".date('d/m/Y H:i')), 0, 1, 'C');
        //DOWNLOAD
        $pdf->Output('I',"Lista de Pais".'.pdf');
        exit;
    }
}
