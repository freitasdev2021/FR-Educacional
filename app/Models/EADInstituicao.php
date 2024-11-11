<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EADInstituicao extends Model
{
    use HasFactory;

    protected $table = "ead_instituicoes";

    protected $fillable = [
        "Nome",
        "DSInstituicao",
        "IDEscola"
    ];
}
