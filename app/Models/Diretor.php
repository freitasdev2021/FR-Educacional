<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diretor extends Model
{
    use HasFactory;

    protected $table = 'diretores';

    protected $fillable = [
        'id',
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
    ];
}
