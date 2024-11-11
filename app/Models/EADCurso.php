<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EADCurso extends Model
{
    use HasFactory;

    protected $table = "ead_cursos";

    protected $fillable = [
        "IDInstituicao",
        "NMCurso",
        "DSCurso"
    ];
}
