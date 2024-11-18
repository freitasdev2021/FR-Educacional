<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atividade extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDAula',
        'STDelete',
        'IDDisciplina',
        'TPConteudo',
        'Pontuacao',
    ];

    public function turma()
    {
        return $this->belongsTo(Turma::class, 'IDTurma');
    }

    public function disciplina()
    {
        return $this->belongsTo(Disciplina::class, 'IDDisciplina');
    }
}
