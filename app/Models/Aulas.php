<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aulas extends Model
{
    use HasFactory;
    protected $table = "aulas";

    protected $fillable = [
        'IDTurma',
        'DSConteudo',
        'DSAula',
        'IDProfessor',
        'IDDisciplina',
        'INIAula',
        'TERAula',
        'STAula',
        'Estagio'
    ];
}
