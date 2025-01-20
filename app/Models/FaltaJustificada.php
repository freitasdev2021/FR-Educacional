<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaltaJustificada extends Model
{
    use HasFactory;

    protected $table = "faltas_justificadas";

    protected $fillable = [
        'IDAluno',
        'Justificativa',
        'HashAula',
    ];
}
