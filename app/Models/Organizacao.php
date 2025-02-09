<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organizacao extends Model
{
    use HasFactory;
    protected $table = 'organizacoes';
    protected $fillable = [
        'Organizacao',
        'Email',
        'Endereco',
        'UF',
        'Cidade',
        'STContrato'
    ];

    public function organizacao()
    {
        return $this->belongsTo(Organizacao::class, 'IDOrg');
    }
}
