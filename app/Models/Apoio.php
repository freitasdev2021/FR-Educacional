<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apoio extends Model
{
    protected $fillable = [
        'IDProfessor',
        'IDAluno',
        'DTInicio',
        'DSEvolucao',
        'DSAcompanhamento',
        'DTTermino'
    ];
}
