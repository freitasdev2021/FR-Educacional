<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ROcorrencia extends Model
{
    use HasFactory;

    protected $table = "respostas_ocorrencia";

    protected $fillable = [
        "IDOcorrencia",
        "Resposta",
        "IDUser"
    ];
}
