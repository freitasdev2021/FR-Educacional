<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alocacao extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDEscola',
        'IDProfissional',
        'INITurno',
        'TERTurno',
    ];

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'IDEscola');
    }

    // Se IDProfissional se refere a um modelo "Profissional",
    // você pode ajustar a relação conforme necessário
    public function profissional()
    {
        return $this->belongsTo(Profissional::class, 'IDProfissional');
    }
}
