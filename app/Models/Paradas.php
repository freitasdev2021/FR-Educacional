<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paradas extends Model
{
    use HasFactory;
    protected $table = "paradas";

    protected $fillable = [
        "IDRota",
        "Nome",
        "Hora"
    ];
}
