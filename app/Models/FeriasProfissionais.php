<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeriasProfissionais extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDEscola',
        'IDProfissional',
        'DTInicio',
        'DTTermino',
        "DSAfastamento"
    ];

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'IDEscola');
    }
}
