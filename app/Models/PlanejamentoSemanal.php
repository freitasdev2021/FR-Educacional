<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanejamentoSemanal extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDPlaanual',
        'PLConteudos',
        'INISemana',
        'TERSemana',
        'Aprovado',
    ];

    protected $casts = [
        'PLConteudos' => 'array',
    ];

    public function planejamentoAnual()
    {
        return $this->belongsTo(PlanejamentoAnual::class, 'IDPlaanual');
    }
}
