<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComentarioPlsemanal extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDPlsemanal',
        'IDPedagogo',
        'Feedback',
    ];

    public function plsemanal()
    {
        return $this->belongsTo(PlanejamentoSemanal::class, 'IDPlsemanal');
    }

    public function pedagogo()
    {
        return $this->belongsTo(Pedagogo::class, 'IDPedagogo');
    }
}
