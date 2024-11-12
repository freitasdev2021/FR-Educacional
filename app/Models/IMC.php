<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IMC extends Model
{
    use HasFactory;

    protected $table = "imc_alunos";

    protected $fillable = [
        "IDAluno",
        "Altura",
        "Peso",
        "IMC"
    ];
}
