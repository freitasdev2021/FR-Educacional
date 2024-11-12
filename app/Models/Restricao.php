<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restricao extends Model
{
    use HasFactory;

    protected $table = "restricoes_alimentares";

    protected $fillable = [
        "NMRestricao",
        "Substituto",
        "IDAluno"
    ];
}
