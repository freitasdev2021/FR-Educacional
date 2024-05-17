<?php

namespace App\Http\Controllers;

abstract class Controller
{
    //FUNÇÃO DE MASCARA CPF E CNPJ PARA IMPRESSÃO NA TELA
    //$cpf = mask($details["cpf"], '###.###.###-##');
    //$cnpj = mask($details["cnpj"], '##.###.###/####-##');
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
