<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ocorrencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDAluno',
        'IDProfessor',
        'DTOcorrencia',
        'DSOcorrido',
    ];

    public function aluno()
    {
        return $this->belongsTo(Aluno::class, 'IDAluno');
    }

    public function professor()
    {
        return $this->belongsTo(Professor::class, 'IDProfessor');
    }
}
