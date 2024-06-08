<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suspenso extends Model
{
    use HasFactory;

    protected $table = 'suspensos';

    protected $fillable = [
        'IDInativo',
        'Justificativa',
        'INISuspensao',
        'TERSuspensao',
    ];

    public function aluno()
    {
        return $this->belongsTo(Aluno::class, 'IDAluno');
    }
}
