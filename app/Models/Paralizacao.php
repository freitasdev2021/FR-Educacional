<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paralizacao extends Model
{
    use HasFactory;
    protected $table = 'paralizacoes';
    protected $fillable = [
        'DSMotivo',
        'IDEscola',
        'DTInicio',
        'DTTermino'
    ];
}
