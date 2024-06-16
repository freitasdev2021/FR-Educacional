<?php

namespace App\Http\Controllers;
use App\Models\Diretor;
use Illuminate\Support\Facades\Auth;
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

    public function getEscolasProfessor($ID){
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

    public static function in_associative_array($array, $chave, $valorProcurado){
        foreach ($array as $subArray) {
            if (isset($subArray[$chave]) && $subArray[$chave] === $valorProcurado){
                return true;
            }
        }
        return false;
    }

    public function getEscolaDiretor($IDDiretor){
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
}
