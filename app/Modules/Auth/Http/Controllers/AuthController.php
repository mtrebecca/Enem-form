<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Modules\Auth\Http\Requests\ForgotPasswordRequest;
use App\Modules\Auth\Http\Requests\LoginRequest;
use App\Modules\Auth\Http\Requests\RegisterRequest;
use App\Modules\Auth\Results\ForgotPasswordOutcome;
use App\Modules\Auth\Results\LoginOutcome;
use App\Modules\Auth\Results\RegisterOutcome;
use App\Modules\Auth\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController
{
    public function __construct(private readonly AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $attempt = $this->authService->register($request->validated());
        $status = match ($attempt->outcome) {
            RegisterOutcome::Success => 201,
            RegisterOutcome::EmailTaken => 422,
        };

        return response()->json($attempt->toResponseBody(), $status);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $attempt = $this->authService->login($request->validated());
        $status = match ($attempt->outcome) {
            LoginOutcome::Success => 200,
            LoginOutcome::InvalidCredentials => 401,
        };

        return response()->json($attempt->toResponseBody(), $status);
    }

    public function logout(Request $request): JsonResponse
    {
        return response()->json($this->authService->logout($request->user()));
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $attempt = $this->authService->forgotPassword($request->validated());
        $status = match ($attempt->outcome) {
            ForgotPasswordOutcome::Sent => 200,
            ForgotPasswordOutcome::EmailNotFound => 404,
        };

        return response()->json($attempt->toResponseBody(), $status);
    }
}
