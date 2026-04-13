<?php

namespace App\Modules\Users\Models;

use App\Modules\Provas\Models\SessaoProva;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password'];

    public function sessoesProva(): HasMany
    {
        return $this->hasMany(SessaoProva::class, 'user_id');
    }
}
