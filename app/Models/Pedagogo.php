<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedagogo extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDEscola',
        'Nome',
        'Nascimento',
        'Admissao',
        'Email',
        'Celular',
        'IDUser',
        'TerminoContrato',
        'CEP',
        'Rua',
        'CPF',
        'UF',
        'Cidade',
        'Bairro',
        'Numero',
        'Ativo',
    ];
}
