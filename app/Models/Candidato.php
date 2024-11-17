<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidato extends Model
{
    use HasFactory;

    protected $table = "candidatos";

    protected $fillable = [
        "Nome",
        "IDUser",
        "Email",
        "Escolaridade",
        "DSCandidato",
        "Telefone",
        "Nascimento",
        "IDOrg"
    ];
}
