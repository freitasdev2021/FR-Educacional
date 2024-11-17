<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessoSeletivo extends Model
{
    use HasFactory;

    protected $table = "processos_seletivos";

    protected $fillable = [
        "Nome",
        "Descricao",
        "DTInscricoes",
        "STProcesso",
        "IDOrg"
    ];
}
