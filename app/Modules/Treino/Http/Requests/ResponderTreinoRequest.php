<?php

namespace App\Modules\Treino\Http\Requests;

use App\Http\Requests\ApiFormRequest;

class ResponderTreinoRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'questao_id' => ['required', 'integer', 'min:1'],
            'opcao_id' => ['nullable', 'integer', 'min:0', 'required_without:resposta'],
            'resposta' => ['nullable', 'string', 'required_without:opcao_id'],
        ];
    }
}
