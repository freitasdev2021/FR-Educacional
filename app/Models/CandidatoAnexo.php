<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidatoAnexo extends Model
{
    use HasFactory;

    protected $table = "candidatos_anexos";

    protected $fillable = [
        "Anexo",
        "Tipo",
        "IDOrg",
        "IDCandidato"
    ];
}
