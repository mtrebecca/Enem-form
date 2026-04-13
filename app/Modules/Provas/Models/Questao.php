<?php

namespace App\Modules\Provas\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Questao extends Model
{
    protected $table = 'questoes';

    protected $fillable = ['prova_id', 'disciplina', 'enunciado', 'opcoes', 'fonte'];

    protected function casts(): array
    {
        return [
            'opcoes' => 'array',
        ];
    }

    public function prova(): BelongsTo
    {
        return $this->belongsTo(Prova::class, 'prova_id');
    }
}
