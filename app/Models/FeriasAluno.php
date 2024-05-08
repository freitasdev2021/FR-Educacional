<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeriasAlunos extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDEscola',
        'DTInicio',
        'IDAluno',
    ];

    public function ferias()
    {
        return $this->belongsTo(Ferias::class, 'IDFerias');
    }

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'IDEscola');
    }

    public function aluno()
    {
        return $this->belongsTo(Aluno::class, 'IDAluno');
    }
}
