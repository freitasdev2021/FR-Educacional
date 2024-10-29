<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    use HasFactory;
    protected $table = 'notas';

    protected $casts = [
        'Nota' => 'decimal',
    ];
    

    protected $fillable = [
        'IDAtividade',
        'IDAluno',
        'Nota'
    ];
}
