<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtividadeAtribuicaoEad extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDAluno',
        'DTEntrega',
        'Realizado',
        'Feedback',
    ];

    public function aluno()
    {
        return $this->belongsTo(Aluno::class, 'IDAluno');
    }
}
