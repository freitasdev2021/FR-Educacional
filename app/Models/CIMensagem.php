<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CIMensagem extends Model
{
    use HasFactory;

    protected $table = "ci_mensagens";

    protected $fillable = [
        "IDUser",
        "Mensagem",
        "IDComunicacao"
    ];
}
