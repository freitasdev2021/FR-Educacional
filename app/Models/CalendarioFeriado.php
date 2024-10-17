<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarioFeriado extends Model
{
    use HasFactory;

    protected $table = "calendario_feriados";

    protected $fillable = [
        "IDEscola",
        "DTInicio",
        "DTTermino",
        "Feriado"
    ];
}
