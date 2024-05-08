<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaltaJustificadaProfissional extends Model
{
    use HasFactory;

    protected $table = 'faltas_justificadas_profissional';

    protected $fillable = [
        'IDPessoa',
        'Justificativa',
        'DTFalta',
    ];

    public function pessoa()
    {
        return $this->belongsTo(Profissional::class, 'IDPessoa');
    }
}
