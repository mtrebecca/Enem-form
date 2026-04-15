<?php

namespace App\Modules\Provas\Http\Requests;

use App\Http\Requests\ApiFormRequest;

class DefinirRespostaRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'opcao_id' => ['nullable', 'integer', 'min:0', 'required_without:resposta'],
            'resposta' => ['nullable', 'string', 'required_without:opcao_id'],
        ];
    }
}
