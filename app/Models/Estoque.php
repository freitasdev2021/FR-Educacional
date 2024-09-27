<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estoque extends Model
{
    use HasFactory;
    protected $table = "estoque";
    protected $fillable = [
        'IDEscola',
        'Quantidade',
        'TPUnidade',
        'Vencimento',
        'TMEmbalagem',
        'Info',
        'CDProduto',
        'Item'
    ];

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'IDEscola');
    }
}
