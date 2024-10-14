<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    use HasFactory;

    protected $table = "professores";
    protected $fillable = [
        'Nome',
        'Nascimento',
        'Admissao',
        'Email',
        'Celular',
        'IDUser',
        'TerminoContrato',
        'CEP',
        'Rua',
        'UF',
        'Cidade',
        'Bairro',
        'Numero',
        'Ativo',
        'CPF'
    ];

}
