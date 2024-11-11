<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VEADCurso extends Model
{
    use HasFactory;

    protected $table = "ead_cursos_turmas";

    protected $fillable = [
        "IDCurso",
        "IDTurma"
    ];
}
