<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Motorista extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDOrganizacao',
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

    public function organizacao()
    {
        return $this->belongsTo(Organizacao::class, 'IDOrganizacao');
    }
}
