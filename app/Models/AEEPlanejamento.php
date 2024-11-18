<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AEEPlanejamento extends Model
{
    use HasFactory;

    protected $table = "planejamento_aee";

    protected $fillable = [
        "IDTurma",
        "Nome",
        "PLConteudos"
    ];
}
