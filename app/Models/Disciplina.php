<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disciplina extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDEscola',
        'NMDisciplina',
        'Obrigatoria',
        'CargaHoraria'
    ];

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'IDEscola');
    }
}
