<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EAD extends Model
{
    use HasFactory;

    protected $table = "EAD";

    protected $fillable = [
        "Orientacao",
        "Normas"
    ];
}
