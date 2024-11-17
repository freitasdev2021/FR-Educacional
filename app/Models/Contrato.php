<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    use HasFactory;

    protected $table = "contratos";

    protected $fillable = [
        "IDProfessor",
        "Nome",
        "Salario",
        "Inicio",
        "Termino",
        "Aditivos",
        "STContrato"
    ];
}
