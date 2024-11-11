<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EADEtapa extends Model
{
    use HasFactory;

    protected $table = "ead_etapas";

    protected $fillable = [
        "IDCurso",
        "NMEtapa",
        "DSEtapa"
    ];
}
