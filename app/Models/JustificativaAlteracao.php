<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JustificativaAlteracao extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDAluno',
        'Justificativa',
    ];

    public function aluno()
    {
        return $this->belongsTo(Aluno::class, 'IDAluno');
    }
}
