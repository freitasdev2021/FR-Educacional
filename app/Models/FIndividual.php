<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FIndividual extends Model
{
    protected $table = "ficha_individual";

    protected $fillable = [
        "IDAluno",
        "Avaliacao"
    ];
}
