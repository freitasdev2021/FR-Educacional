<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chamada extends Model
{
    use HasFactory;

    protected $table = 'frequencia';

    protected $fillable = [
        'Presenca',
        'IDAluno',
        'IDAula',
        'HashAula',
        'created_at'
    ];
}
