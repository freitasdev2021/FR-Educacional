<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diretor extends Model
{
    use HasFactory;

    protected $table = 'diretores';

    protected $fillable = [
        'id',
        'IDEscola',
        'Nome',
        'Email',
        'TPContrato'
    ];
}
