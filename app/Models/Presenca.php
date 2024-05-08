<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presenca extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDAula',
        'IDEscola',
        'IDTurma',
        'IDProfessor',
        'IDAluno',
        'Status',
    ];

    public function aula()
    {
        return $this->belongsTo(Aula::class, 'IDAula');
    }

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'IDEscola');
    }

    public function turma()
    {
        return $this->belongsTo(Turma::class, 'IDTurma');
    }

    public function professor()
    {
        return $this->belongsTo(Professor::class, 'IDProfessor');
    }

    public function aluno()
    {
        return $this->belongsTo(Aluno::class, 'IDAluno');
    }
}
