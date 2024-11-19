<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Situacao extends Model
{
    use HasFactory;
    protected $table = 'alteracoes_situacao';

    protected $fillable = [
        'IDAluno',
        'Justificativa',
        'STAluno',
        'DTSituacao'
    ];
}
