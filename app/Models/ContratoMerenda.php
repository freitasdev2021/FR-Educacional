<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContratoMerenda extends Model
{
    use HasFactory;

    protected $table = "contratos_merenda";

    protected $fillable = [
        "IDOrg",
        "NMEmpresa",
        "Vigencia",
        "VLContrato",
        "NProcesso",
        "Empenho",
        "AF"
    ];
}
