<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matriculas extends Model
{
    use HasFactory;

    protected $table = 'matriculas';

    protected $fillable = [
        'Nome',
        'CPF',
        'RG',
        'CEP',
        'Rua',
        'Email',
        'Celular',
        'UF',
        'Cidade',
        'BolsaFamilia',
        'Alergia',
        'Transporte',
        'NEE',
        'AMedico',
        'APsicologico',
        'Aprovado',
        'Nascimento',
        'Foto',
        'Bairro',
        'Numero',
        'CDPasta',
        'Quilombola',
        'DireitoImagem',
        'EReligioso',
        'EFisica',
        'PaisJSON',
        'Sexo',
        'Cor',
        'Observacoes',
        'Passaporte',
        'CNascimento',
        'SUS',
        'CNH',
        'NIS',
        'INEP',
        'Naturalidade',
        'IDRota',
        'TPSangue',
        'Expedidor',
        'CNascimento'
    ];

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'IDEscola');
    }
}
