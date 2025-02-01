<?php

namespace App\Http\Controllers;
use App\Models\Diretor;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;

abstract class Controller
{
    //FUNÇÃO DE MASCARA CPF E CNPJ PARA IMPRESSÃO NA TELA
    //$cpf = mask($details["cpf"], '###.###.###-##');
    //$cnpj = mask($details["cnpj"], '##.###.###/####-##');

    public function getDados(){
        return [
            'org' => Auth::user()->id_org,
            'tipo' => Auth::user()->tipo
        ];
    }

    public function randomHash() {
        // Gerar uma string única baseada no ID exclusivo da máquina, timestamp, e dados aleatórios.
        $dadosUnicos = uniqid(bin2hex(random_bytes(10)), true);
    
        // Gerar o hash final usando SHA-256 para maior segurança.
        return hash('sha256', $dadosUnicos);
    }

    public static function cpfCnpj($val, $mask) {
        $maskared = '';
        $k = 0;
        for($i = 0; $i<=strlen($mask)-1; $i++) {
            if($mask[$i] == '#') {
                if(isset($val[$k])) $maskared .= $val[$k++];
            } else {
                if(isset($mask[$i])) $maskared .= $mask[$i];
            }
        }
        return $maskared;
    }

    public function getCurrentEscolasProfessor($ID){
        $SQL = <<<SQL
        SELECT 
            e.id as IDEscola
        FROM 
            escolas e 
        INNER JOIN alocacoes al ON(e.id = al.IDEscola) 
        INNER JOIN users us ON(us.IDProfissional = al.IDProfissional ) 
        WHERE us.id = $ID AND al.TPProfissional = 'PROF'
        SQL; 

        $arr = [];
        $consulta = DB::select($SQL);
        foreach($consulta as $c){
            array_push($arr,$c->IDEscola);
        }

        return $arr;

    }

    public static function getFichaProfessor($id,$TipoFicha){
        $SQL = <<<SQL
        SELECT 
            t.id as IDTurma,
            d.id as IDDisciplina,
            tn.INITur Inicio,
            tn.TERTur Termino,
            e.Nome as Escola,
            t.Nome as Turma,
            t.Serie as Serie,
            tn.DiaSemana as Dia,
            d.NMDisciplina as Disciplina
        FROM turnos tn
        INNER JOIN turmas t ON(tn.IDTurma = t.id)
        INNER JOIN alocacoes al ON(t.IDEscola = al.IDEscola)
        INNER JOIN escolas e ON(al.IDEscola = e.id)
        INNER JOIN professores p ON(p.id = tn.IDProfessor)
        INNER JOIN users us ON(us.IDProfissional = p.id)
        INNER JOIN disciplinas d ON(d.id = tn.IDDisciplina)
        WHERE us.id = $id GROUP BY tn.INITur,tn.TERTur,tn.DiaSemana ORDER BY tn.DiaSemana
        SQL;

        if($TipoFicha == 'Horarios'){
            $return = DB::select($SQL);
        }elseif($TipoFicha == 'Turmas'){
            $return = [];
            $consulta = DB::select($SQL);
            foreach($consulta as $c){
                array_push($return,$c->IDTurma);
            }
        }elseif($TipoFicha == 'Disciplinas'){
            $return = [];
            $consulta = DB::select($SQL);
            foreach($consulta as $c){
                array_push($return,array('IDDisciplina'=>$c->IDDisciplina,'Disciplina'=>$c->Disciplina));
            }
        }

        return $return;
    }

    public static function timeToNumber($time) {
        list($hours, $minutes) = explode(':', $time);
        return $hours + ($minutes / 60);
    }

    public static function decimalToMin($dec){
        $cargaHoraria = $dec * (50 / 60);

        return $cargaHoraria;
    }
    
    
    public static function utfConvert($String){
        return mb_convert_encoding($String,'ISO-8859-1', 'UTF-8');
    }

    public static function alternativeUsData($dt){
        $dtExplode = explode('/',$dt);
        $data = $dtExplode[2]."-".$dtExplode[1]."-".$dtExplode[0];

        return $data;
    }

    public static function in_associative_array($array, $chave, $valorProcurado){
        foreach ($array as $subArray) {
            if (isset($subArray[$chave]) && $subArray[$chave] === $valorProcurado){
                return true;
            }
        }
        return false;
    }

    public static function criarCabecalho($pdf, $nomeEscola, $nomeMunicipio, $caminhoImagem,$Documento,$Endereco){
        // Adicionar imagem no canto esquerdo
        $pdf->Image($caminhoImagem, 10, 10, 30); // (x, y, largura)

        // Definir fonte para o cabeçalho
        $pdf->SetFont('Arial', 'B', 12);

        // Definir a posição inicial do texto (centralizado horizontalmente)
        $pdf->SetXY(30, 10); // Ajuste o X e Y para posicionar o texto após a imagem

        // Adicionar o nome da escola (centralizado)
        $pdf->Cell(0, 10, self::utfConvert($nomeEscola), 0, 1, 'C'); // Texto centralizado com quebra de linha

        // Adicionar o nome do município (centralizado)
        $pdf->SetXY(30, 20); // Ajuste a posição para a próxima linha
        $pdf->Cell(0, 10, self::utfConvert($nomeMunicipio), 0, 1, 'C');
        
        // Adicionar o nome do município (centralizado)
        $pdf->SetXY(30, 30); // Ajuste a posição para a próxima linha
        $pdf->Cell(0, 10, self::utfConvert($Endereco['Rua'].", ".$Endereco['Numero']." ".$Endereco['Bairro']." - ".$Endereco['Cidade']."/".$Endereco['UF']), 0, 1, 'C');

        $pdf->SetFont('Arial','B',15);
        $pdf->SetXY(25, 40); // Ajuste a posição para a próxima linha
        $pdf->Cell(0, 10, self::utfConvert($Documento), 0, 1, 'C'); // Texto centralizado com quebra de linha
        // Espaçamento após o cabeçalho
        $pdf->Ln();
    }


    public function upload(string $file, string $url,bool $edit){
        if(!$file){
            return false;
        }

        if($edit){
            $Arquivo = $request->file('RGPaisAnexo')->getClientOriginalName();
            Storage::disk('public')->delete('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'. $request->CDPasta . '/' . $request->oldRGPaisAnexo);
            $request->file('RGPaisAnexo')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$request->CDPasta,$RGPaisAnexo,'public');
        }else{
            $RGPaisAnexo = '';
        }

        return $Arquivo;
    }

    public static function getEscolaDiretor($IDDiretor){
        $dirID = DB::select("SELECT IDProfissional FROM users WHERE id = $IDDiretor ")[0];
        $dir = Diretor::select('IDEscola')->where('id',$dirID->IDProfissional)->first();
        return $dir->IDEscola;
    }

    public static function array_associative_unique($array) {
        $uniqueArray = [];
        $uniqueCheck = [];
    
        foreach ($array as $element) {
            // Converter stdClass para array associativo
            if (is_object($element)) {
                $element = (array)$element;
            }
    
            // Cria uma chave única para cada elemento
            $jsonElement = json_encode($element);
    
            // Adiciona ao array único se ainda não estiver presente
            if (!in_array($jsonElement, $uniqueCheck)) {
                $uniqueCheck[] = $jsonElement;
                $uniqueArray[] = $element;
            }
        }
    
        return $uniqueArray;
    }

    //MASCARA PARA TELEFONE
    public static function formataTelefone($numero){
        if(strlen($numero) == 10){
            $novo = substr_replace($numero, '(', 0, 0);
            $novo = substr_replace($novo, '9', 5, 0);
            $novo = substr_replace($novo, ')', 3, 0);
        }else{
            $novo = substr_replace($numero, '(', 0, 0);
            $novo = substr_replace($novo, ')', 3, 0);
            $novo = substr_replace($novo, '-', 9, 0);
            $novo = substr_replace($novo, ' ', 4, 0);
            $novo = substr_replace($novo, ' ', 6, 0);
        }
        return $novo;
    }
    //MASCARA PARA DATA
    public static function data($data,$tipo){
        return date($tipo, strtotime($data));
    }

    public static function diaSemana($data) {
        $diasSemana = [
            'Sunday' => 'domingo',
            'Monday' => 'segunda-feira',
            'Tuesday' => 'terça-feira',
            'Wednesday' => 'quarta-feira',
            'Thursday' => 'quinta-feira',
            'Friday' => 'sexta-feira',
            'Saturday' => 'sábado'
        ];
    
        $diaIngles = date('l', strtotime($data)); // Obter o dia da semana em inglês
        return $diasSemana[$diaIngles] ?? 'Dia inválido';
    }
    
}
