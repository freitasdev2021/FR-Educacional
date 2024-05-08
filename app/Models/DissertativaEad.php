<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DissertativaEad extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDAtividade',
        'Enunciado',
        'Resposta',
        'Feedback',
        'Resultado',
        'Total',
        'Nota',
    ];

    public function atividade()
    {
        return $this->belongsTo(AtividadeEad::class, 'IDAtividade');
    }
}
