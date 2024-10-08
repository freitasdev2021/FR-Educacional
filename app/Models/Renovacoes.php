<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Renovacoes extends Model
{
    protected $table = 'renovacoes';

    protected $fillable = [
        'IDAluno',
        'Aprovado',
        'ANO'
    ];

    public function aluno()
    {
        return $this->belongsTo(Aluno::class, 'IDAluno');
    }
}
