<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transferencia extends Model
{
    use HasFactory;
    protected $table = 'transferencias';
    protected $fillable = [
        'IDAluno',
        'Aprovado',
        'IDEscolaDestino',
        'IDEscolaOrigem',
        'Justificativa',
        'DTTransferencia'
    ];

    public function aluno()
    {
        return $this->belongsTo(Aluno::class, 'IDAluno');
    }

    public function escolaDestino()
    {
        return $this->belongsTo(Escola::class, 'IDEscolaDestino');
    }
}
