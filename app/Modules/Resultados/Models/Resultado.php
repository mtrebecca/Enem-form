<?php

namespace App\Modules\Resultados\Models;

use App\Modules\Provas\Models\SessaoProva;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resultado extends Model
{
    protected $table = 'resultados';

    public $timestamps = false;

    protected $fillable = [
        'sessao_id',
        'total_questoes',
        'total_acertos',
        'percentual_acerto',
        'detalhe_disciplinas',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'percentual_acerto' => 'decimal:2',
            'detalhe_disciplinas' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function sessao(): BelongsTo
    {
        return $this->belongsTo(SessaoProva::class, 'sessao_id');
    }
}
