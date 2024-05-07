<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Renovacao extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDAluno',
        'Aprovado',
        'ANO',
    ];

    public function aluno()
    {
        return $this->belongsTo(Aluno::class, 'IDAluno');
    }
}
