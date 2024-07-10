<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtividadeAtribuicao extends Model
{
    use HasFactory;

    protected $table = 'atividades_atribuicoes';
    protected $fillable = [
        'IDAluno',
        'DTEntrega',
        'Realizado',
        'Feedback',
        'IDAtividade'
    ];

    public function aluno()
    {
        return $this->belongsTo(Aluno::class, 'IDAluno');
    }
}
