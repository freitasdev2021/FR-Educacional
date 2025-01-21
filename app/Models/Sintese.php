<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sintese extends Model
{
    protected $table = "sintese_aprendizagem";

    protected $fillable = [
        "IDDisciplina",
        "Referencia",
        "Sintese"
    ];
}
