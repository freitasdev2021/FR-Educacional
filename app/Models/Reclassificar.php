<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reclassificar extends Model
{
    use HasFactory;

    protected $table = "reclassificacoes";

    protected $fillable = [
        "IDTurmaAntiga",
        "IDTurmaNova",
        "IDAluno",
        "IDEscola"
    ];
}
