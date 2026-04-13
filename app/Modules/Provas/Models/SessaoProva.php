<?php

namespace App\Modules\Provas\Models;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SessaoProva extends Model
{
    protected $table = 'sessoes_prova';

    public $timestamps = false;

    protected $fillable = ['user_id', 'prova_id', 'status', 'iniciada_em', 'finalizada_em', 'respostas'];

    protected function casts(): array
    {
        return [
            'iniciada_em' => 'datetime',
            'finalizada_em' => 'datetime',
            'respostas' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function prova(): BelongsTo
    {
        return $this->belongsTo(Prova::class, 'prova_id');
    }

    public function resultado(): HasOne
    {
        return $this->hasOne(\App\Modules\Resultados\Models\Resultado::class, 'sessao_id');
    }
}
