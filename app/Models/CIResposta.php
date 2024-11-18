<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CIResposta extends Model
{
    use HasFactory;

    protected $table = "ci_respostas";

    protected $fillable = [
        "IDComunicacao",
        "IDUser",
        "Resposta"
    ];
}
