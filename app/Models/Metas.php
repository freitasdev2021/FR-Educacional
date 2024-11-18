<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Metas extends Model
{
    use HasFactory;

    protected $table = "metas";

    protected $fillable = [
        "MSituacional",
        "MConceitual",
        "MOperacional",
        "MetasJSON",
        "IDEscola"
    ];
}
