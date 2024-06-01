<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rodagem extends Model
{
    use HasFactory;
    protected $table = "rodagem";

    protected $fillable = [
        'IDVeiculo',
        'IDRota',
        'KMInicial',
        'KMFinal'
    ];
}
