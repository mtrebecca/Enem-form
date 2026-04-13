<?php

namespace App\Modules\Users\Http\Controllers;

use App\Modules\Provas\Services\ProvasService;
use App\Modules\Users\Services\UsersService;
use App\Support\RequestUserId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsersController
{
    public function __construct(
        private readonly UsersService $usersService,
        private readonly ProvasService $provasService,
    ) {}

    public function dashboard(Request $request): JsonResponse
    {
        $userId = RequestUserId::require($request);

        return response()->json([
            'provas' => $this->provasService->listar(),
            'historico' => $this->usersService->historico($userId),
        ]);
    }

    public function minhaConta(Request $request): JsonResponse
    {
        $userId = RequestUserId::require($request);

        return response()->json($this->usersService->minhaConta($userId));
    }

    public function historico(Request $request): JsonResponse
    {
        $userId = RequestUserId::require($request);

        return response()->json($this->usersService->historico($userId));
    }
}
