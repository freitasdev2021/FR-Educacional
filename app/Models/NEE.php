<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NEE extends Model
{
    use HasFactory;

    protected $table = "necessidades_aluno";

    protected $fillable = [
        "IDAluno",
        "CID",
        "Laudo",
        "DTLaudo",
        "DSNecessidade",
        "DSAcompanhamento"
    ];
}
