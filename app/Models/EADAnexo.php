<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EADAnexo extends Model
{
    use HasFactory;

    protected $table = "ead_anexos";

    protected $fillable = [
        "IDAula",
        "Anexo",
        "Tipo"
    ];
}
