<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Veiculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDOrganizacao',
        'Nome',
        'Marca',
        'Placa',
        'Cor',
        'KMAquisicao'
    ];

    public function organizacao()
    {
        return $this->belongsTo(Organizacao::class, 'IDOrganizacao');
    }
}
