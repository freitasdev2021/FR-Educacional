<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidatoCurso extends Model
{
    use HasFactory;

    protected $table  = "candidatos_cursos";

    protected $fillable = [
        "Nome",
        "Tipo",
        "Instituicao",
        "INICurso",
        "TERCurso",
        "IDCandidato"
    ];
}
