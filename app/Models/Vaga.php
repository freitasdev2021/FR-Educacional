<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vaga extends Model
{
    use HasFactory;

    protected $table = "vagas";

    protected $fillable = [
        "IDEscola",
        "Faixa",
        "INIMatricula",
        "TERMatricula",
        "QTVagas"
    ];
}
