<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remanejo extends Model
{
    use HasFactory;

    protected $table = "remanejados";

    protected $fillable = [
        'IDAluno',
        'IDTurmaOrigem',
        'IDTurmaDestino',
        'IDEscola'
    ];
}
