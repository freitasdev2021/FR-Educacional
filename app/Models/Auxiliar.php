<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auxiliar extends Model
{
    use HasFactory;
    protected $table = "Auxiliares";
    protected $fillable = [
        'IDEscola',
        'Nome',
        'Nascimento',
        'Admissao',
        'Tipo',
        'IDUser',
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
