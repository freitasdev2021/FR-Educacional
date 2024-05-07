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
        'TerminoContrato',
        'CEP',
        'Rua',
        'UF',
        'Cidade',
        'Bairro',
        'Numero',
        'Ativo',
    ];
}
