<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Espera extends Model
{
    use HasFactory;

    protected $table = "lista_espera";

    protected $fillable = [
        "IDEscola",
        "Aluno",
        "Contato",
        "Observacoes"
    ];
}
