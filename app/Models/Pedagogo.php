<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedagogo extends Model
{
    use HasFactory;

    protected $fillable = [
        'Nome',
        'Email',
        'IDUser',
        'Ativo',
        'TPContrato'
    ];
}
