<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackTransferencia extends Model
{
    use HasFactory;
    protected $table = 'feedback_transferencias';
    protected $fillable = [
        'IDTransferencia',
        'Feedback',
    ];

    public function aluno()
    {
        return $this->belongsTo(Aluno::class, 'IDAluno');
    }
}
