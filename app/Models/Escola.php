<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escola extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDOrg',
        'Nome',
        'CEP',
        'Rua',
    ];

    public function organizacao()
    {
        return $this->belongsTo(Organizacao::class, 'IDOrg');
    }
}
