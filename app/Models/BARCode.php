<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BARCode extends Model
{
    use HasFactory;

    protected $table = "cod_livros";

    protected $fillable = [
        "Codigo",
        "IDOrg"
    ];
}
