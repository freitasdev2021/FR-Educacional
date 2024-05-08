<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDProfessor',
        'IDDisciplina',
        'IDTurma',
        'INITur',
        'TERTur',
    ];

    public function professor()
    {
        return $this->belongsTo(Professor::class, 'IDProfessor');
    }

    public function disciplina()
    {
        return $this->belongsTo(Disciplina::class, 'IDDisciplina');
    }

    public function turma()
    {
        return $this->belongsTo(Turma::class, 'IDTurma');
    }
}
