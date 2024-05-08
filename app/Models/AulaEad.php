<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AulaEad extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDTurma',
        'DescricaoAula',
    ];

    public function turma()
    {
        return $this->belongsTo(Turma::class, 'IDTurma');
    }
}
