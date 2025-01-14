<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escola extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDOrg',
        'Nome',
        'Foto',
        'CEP',
        'Rua',
        'Bairro',
        'Cidade',
        'Numero',
        'UF',
        'Telefone',
        'Email',
        'QTVagas',
        'INIFuncionamento',
        'TERFuncionamento',
        'OBSGeralHistorico',
        'OBSAta',
        'IDCenso'
    ];

    public function organizacao()
    {
        return $this->belongsTo(Organizacao::class, 'IDOrg');
    }
}
