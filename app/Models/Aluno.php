<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aluno extends Model
{
    use HasFactory;
    protected $table = 'alunos';
    
    protected $fillable = [
        'IDMatricula',
        'STAluno',
        'IDTurma',
        'IDUser',
        "DTEntrada",
        "DTSaida"
    ];

    public function matricula()
    {
        return $this->belongsTo(Matricula::class, 'IDMatricula');
    }
}
