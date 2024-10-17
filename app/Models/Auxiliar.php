<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auxiliar extends Model
{
    use HasFactory;
    protected $table = "auxiliares";
    protected $fillable = [
        'IDEscola',
        'Nome',
        'Nascimento',
        'Admissao',
        'Tipo',
        'IDUser',
        'Email',
        'CPF',
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
