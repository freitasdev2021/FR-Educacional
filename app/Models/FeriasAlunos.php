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
        'DTTermino',
        'IDAluno',
    ];

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'IDEscola');
    }
}
