<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CI extends Model
{
    use HasFactory;

    protected $table = "comunicacao_interna";

    protected $fillable = [
        "IDOrg",
        "Assunto",
        "Mensagem",
        "AnexosJSON",
        "STComunicacao"
    ];
}
