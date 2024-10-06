<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anexo extends Model
{
    use HasFactory;

    protected $table = "anexos_aluno";

    protected $fillable = [
        "IDAluno",
        "DSAnexo",
        "Anexo"
    ];
}
