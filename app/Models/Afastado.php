<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Afastado extends Model
{
    use HasFactory;

    protected $fillable = [
        'IDInativo',
        'Justificativa',
        'INISuspensao',
        'TERSuspensao',
    ];
}
