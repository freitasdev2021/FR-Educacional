<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarioRecuperacao extends Model
{
    use HasFactory;

    protected $table = "calendario_recuperacoes";

    protected $fillable = [
        "IDEscola",
        "DTInicio",
        "DTTermino",
        "Recuperacao"
    ];
}
