<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aulas extends Model
{
    use HasFactory;
    protected $table = "aulas";

    protected $fillable = [
        'id',
        'IDTurma',
        'DSConteudo',
        'DSAula',
        'IDProfessor',
        'IDDisciplina',
        'STAula',
        'Estagio',
        'DTAula',
        'Hash'
    ];
}
