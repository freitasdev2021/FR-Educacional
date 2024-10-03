<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recuperacao extends Model
{
    use HasFactory;

    protected $table = "recuperacao";

    protected $fillable = [
        "IDAluno",
        "IDDisciplina",
        "Estagio"  
    ];
}
