<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Modules\Auth\Results\ForgotPasswordOutcome;
use App\Modules\Auth\Results\LoginOutcome;
use App\Modules\Auth\Results\RegisterOutcome;
use App\Modules\Auth\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController
{
    public function __construct(private readonly AuthService $authService) {}

    public function register(Request $request): JsonResponse
    {
        $attempt = $this->authService->register($request->all());
        $status = match ($attempt->outcome) {
            RegisterOutcome::Success => 201,
            RegisterOutcome::EmailRequired, RegisterOutcome::EmailTaken => 422,
        };

        return response()->json($attempt->toResponseBody(), $status);
    }

    public function login(Request $request): JsonResponse
    {
        $attempt = $this->authService->login($request->all());
        $status = match ($attempt->outcome) {
            LoginOutcome::Success => 200,
            LoginOutcome::InvalidCredentials => 401,
        };

        return response()->json($attempt->toResponseBody(), $status);
    }

    public function logout(): JsonResponse
    {
        return response()->json($this->authService->logout());
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $attempt = $this->authService->forgotPassword($request->all());
        $status = match ($attempt->outcome) {
            ForgotPasswordOutcome::Sent => 200,
            ForgotPasswordOutcome::EmailNotFound => 404,
        };

        return response()->json($attempt->toResponseBody(), $status);
    }
}
