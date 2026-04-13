<?php

namespace App\Modules\Provas\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prova extends Model
{
    protected $table = 'provas';

    protected $fillable = ['titulo', 'status', 'tipo'];

    public function questoes(): HasMany
    {
        return $this->hasMany(Questao::class, 'prova_id');
    }

    public function sessoes(): HasMany
    {
        return $this->hasMany(SessaoProva::class, 'prova_id');
    }
}
