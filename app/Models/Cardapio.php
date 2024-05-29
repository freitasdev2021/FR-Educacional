<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cardapio extends Model
{
    use HasFactory;

    protected $table = "cardapio";
    protected $fillable = [
        'IDEscola',
        'Dia',
        'Turno',
        'Descricao',
        'Foto',
    ];

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'IDEscola');
    }
}
