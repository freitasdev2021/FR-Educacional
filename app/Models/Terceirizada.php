<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Terceirizada extends Model
{
    use HasFactory;
    protected $table = "terceirizadas";
    protected $fillable = [
        'IDOrg',
        'Nome',
        'CEP',
        'Rua',
        'Bairro',
        'Cidade',
        'Numero',
        'UF',
        'Telefone',
        'Email',
        'CNPJ',
        'Ramo',
        'TerminoContrato'
    ];

    public function organizacao()
    {
        return $this->belongsTo(Organizacao::class, 'IDOrg');
    }
}
