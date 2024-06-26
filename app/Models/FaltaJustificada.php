<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaltaJustificada extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDPessoa',
        'Justificativa',
        'DTFalta',
    ];
}
