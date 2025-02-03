<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CClasse extends Model
{
    protected $table = "conselho_classe";

    protected $fillable = [
        "IDAluno",
        "Nota",
        "IDDisciplina",
        "Situacao"
    ];
}
