<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apoio extends Model
{
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

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'IDEscola');
    }
}
