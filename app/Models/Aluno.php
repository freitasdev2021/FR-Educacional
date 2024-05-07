<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aluno extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDMatricula',
        'STAluno',
    ];

    public function matricula()
    {
        return $this->belongsTo(Matricula::class, 'IDMatricula');
    }
}
