<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObjetivaEad extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDAtividade',
        'Enunciado',
        'Opcoes',
        'Correta',
        'Resposta',
        'Feedback',
        'Total',
    ];

    public function atividade()
    {
        return $this->belongsTo(AtividadeEad::class, 'IDAtividade');
    }
}
