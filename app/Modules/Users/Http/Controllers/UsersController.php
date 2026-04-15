<?php

namespace App\Modules\Users\Http\Controllers;

use App\Support\ActiveProvasCatalog;
use App\Modules\Users\Services\UsersService;
use App\Support\RequestUserId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsersController
{
    public function __construct(
        private readonly UsersService $usersService,
        private readonly ActiveProvasCatalog $provasCatalog,
    ) {}

    public function dashboard(Request $request): JsonResponse
    {
        $userId = RequestUserId::require($request);

        return response()->json([
            'provas' => $this->provasCatalog->listar(),
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
