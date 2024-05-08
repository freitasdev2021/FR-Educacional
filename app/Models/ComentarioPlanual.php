<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComentarioPlanual extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDPlanual',
        'IDPedagogo',
        'Feedback',
    ];

    public function planual()
    {
        return $this->belongsTo(PlanejamentoAnual::class, 'IDPlanual');
    }

    public function pedagogo()
    {
        return $this->belongsTo(Pedagogo::class, 'IDPedagogo');
    }
}
