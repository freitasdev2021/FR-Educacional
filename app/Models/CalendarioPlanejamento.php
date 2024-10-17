<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarioPlanejamento extends Model
{
    use HasFactory;

    protected $table = "calendario_planejamentos";

    protected $fillable = [
        "IDEscola",
        "DTInicio",
        "DTTermino",
        "Assunto"
    ];
}
