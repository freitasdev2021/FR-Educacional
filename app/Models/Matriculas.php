<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matriculas extends Model
{
    use HasFactory;

    protected $table = 'matriculas';

    protected $fillable = [
        'AnexoRG',
        'CResidencia',
        'Historico',
        'Nome',
        'CPF',
        'RG',
        'CEP',
        'Rua',
        'Email',
        'Celular',
        'UF',
        'Cidade',
        'AnoLetivo',
        'BolsaFamilia',
        'Alergia',
        'Transporte',
        'NEE',
        'AMedico',
        'APsicologico',
        'Aprovado',
        'Nascimento',
        'Foto',
        'Numero',
        'Bairro',
        'CDPasta'
    ];

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'IDEscola');
    }
}
