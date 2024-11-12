<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leitor extends Model
{
    use HasFactory;

    protected $table = "leitores";

    protected $fillable = [
        "Nome",
        "Nascimento",
        "EnderecoJSON"
    ];
}
