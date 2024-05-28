<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class alocacoesDisciplinas extends Model
{
    use HasFactory;
    protected $table = 'alocacoes_disciplinas';

    protected $fillable = [
        'IDEscola',
        'IDDisciplina'
    ];
}
