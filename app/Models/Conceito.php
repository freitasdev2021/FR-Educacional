<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conceito extends Model
{
    use HasFactory;

    protected $table = "conceitos";

    protected $fillable = [
        "IDTurma",
        "NMConceito",
        "ConceitosJSON",
        "Etapa"
    ];
}
