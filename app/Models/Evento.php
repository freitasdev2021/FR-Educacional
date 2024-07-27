<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;
    protected $table = 'eventos';
    protected $fillable = [
        'IDEscola',
        'DSEvento',
        'Data',
        'Inicio',
        'Termino',
    ];

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'IDEscola');
    }
}
