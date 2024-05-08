<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turma extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDEscola',
        'Serie',
        'Nome',
        'INITurma',
        'TERTurma',
        'Periodo',
        'NotaPeriodo',
        'MediaPeriodo',
        'TotalAno',
    ];

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'IDEscola');
    }
}
