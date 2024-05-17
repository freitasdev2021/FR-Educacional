<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendario extends Model
{
    use HasFactory;
    protected $table = 'calendario';
    protected $fillable = [
        'IDEscola',
        'INIAno',
        'TERAno',
    ];

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'IDEscola');
    }
}
