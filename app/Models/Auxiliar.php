<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auxiliar extends Model
{
    use HasFactory;
    protected $table = "auxiliares";
    protected $fillable = [
        'IDEscola',
        'Nome',
        'Tipo',
        'IDUser',
        'Email',
        'Ativo',
        'TPContrato'
    ];

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'IDEscola');
    }
}
