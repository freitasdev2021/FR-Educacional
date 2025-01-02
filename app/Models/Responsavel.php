<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Responsavel extends Model
{
    use HasFactory;
    protected $table = 'responsavel';
    protected $fillable = [
        'IDAluno',
        'RGPais',
        'NMResponsavel',
        'EmailResponsavel',
        'CLResponsavel',
        'CPFResponsavel'
    ];

    public function aluno()
    {
        return $this->belongsTo(Aluno::class, 'IDAluno');
    }
}
