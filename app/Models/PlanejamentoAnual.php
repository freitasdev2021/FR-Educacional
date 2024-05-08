<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanejamentoAnual extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDProfessor',
        'IDDisciplina',
        'IDTurma',
        'PLConteudos',
        'Aprovado',
    ];

    protected $casts = [
        'PLConteudos' => 'array',
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
