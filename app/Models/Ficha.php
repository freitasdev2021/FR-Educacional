<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ficha extends Model
{
    use HasFactory;

    protected $table = "ficha_avaliativa";

    protected $fillable = [
        "IDEscola",
        "Titulo",
        "Formulario"
    ];
}
