<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class participacoesEvento extends Model
{
    use HasFactory;

    protected $table = 'participacoesEventos';
    
    protected $fillable = [
        'IDEscola',
        'IDEvento',
        'DTInicio',
        'DTTermino'
    ];
}
