<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apoio extends Model
{

    protected $table = 'apoio';

    protected $fillable = [
        'IDProfessor',
        'IDAluno',
        'DTInicio',
        'DSEvolucao',
        'CID',
        'DSAcompanhamento',
        'DTTermino'
    ];
}
