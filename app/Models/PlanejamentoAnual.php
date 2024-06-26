<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanejamentoAnual extends Model
{
    use HasFactory;
    protected $table = 'planejamentoanual';
    protected $fillable = [
        'IDProfessor',
        'IDDisciplina',
        'PLConteudos',
        'Aprovado',
        'NMPlanejamento'
    ];

    protected $casts = [
        'PLConteudos' => 'array',
    ];

    public function professor()
    {
        return $this->belongsTo(Professor::class, 'IDProfessor');
    }

    public function disciplina()
    {
        return $this->belongsTo(Disciplina::class, 'IDDisciplina');
    }
}
