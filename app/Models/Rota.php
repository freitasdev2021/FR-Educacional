<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rota extends Model
{
    use HasFactory;

    protected $table = "rotas";
    protected $fillable = [
        'IDVeiculo',
        'IDMotorista',
        'Descricao',
        'DiasJSON',
        'Distancia',
        'Turno',
        'Partida',
        'Chegada',
        'HoraPartida',
        'HoraChegada'
    ];

    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class, 'IDVeiculo');
    }

    public function motorista()
    {
        return $this->belongsTo(Motorista::class, 'IDMotorista');
    }
}
