<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstoqueMovimentacao extends Model
{
    use HasFactory;

    protected $table = 'estoque_movimentacao';

    protected $fillable = [
        'IDEstoque',
        'TPMovimentacao',
    ];

    public function estoque()
    {
        return $this->belongsTo(Estoque::class, 'IDEstoque');
    }
}
