<?php

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Results\ForgotPasswordAttempt;
use App\Modules\Auth\Results\ForgotPasswordOutcome;
use App\Modules\Auth\Results\LoginAttempt;
use App\Modules\Auth\Results\LoginOutcome;
use App\Modules\Auth\Results\RegisterAttempt;
use App\Modules\Auth\Results\RegisterOutcome;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $payload): RegisterAttempt
    {
        $email = (string) ($payload['email'] ?? '');

        if ($email === '') {
            return new RegisterAttempt(
                RegisterOutcome::EmailRequired,
                'Email e obrigatorio.',
            );
        }

        if (User::query()->where('email', $email)->exists()) {
            return new RegisterAttempt(
                RegisterOutcome::EmailTaken,
                'Este email ja esta cadastrado.',
            );
        }

        $user = User::query()->create([
            'name' => (string) ($payload['nome'] ?? 'Novo Usuario'),
            'email' => $email,
            'password' => Hash::make((string) ($payload['senha'] ?? '123456')),
        ]);

        return new RegisterAttempt(
            RegisterOutcome::Success,
            'Usuario cadastrado com sucesso',
            base64_encode("mock-token-{$user->id}"),
            [
                'id' => $user->id,
                'nome' => $user->name,
                'email' => $user->email,
            ],
        );
    }

    public function login(array $payload): LoginAttempt
    {
        $email = $payload['email'] ?? '';
        $senha = $payload['senha'] ?? '';

        $user = User::query()->where('email', $email)->first();
        if (! $user || ! Hash::check((string) $senha, $user->password)) {
            return new LoginAttempt(
                LoginOutcome::InvalidCredentials,
                'Credenciais invalidas',
            );
        }

        return new LoginAttempt(
            LoginOutcome::Success,
            'Login realizado com sucesso',
            base64_encode("mock-token-{$user->id}"),
            [
                'id' => $user->id,
                'nome' => $user->name,
                'email' => $user->email,
            ],
        );
    }

    public function logout(): array
    {
        return ['message' => 'Logout realizado com sucesso'];
    }

    public function forgotPassword(array $payload): ForgotPasswordAttempt
    {
        $email = (string) ($payload['email'] ?? '');
        $found = User::query()->where('email', $email)->exists();

        if ($found) {
            return new ForgotPasswordAttempt(
                ForgotPasswordOutcome::Sent,
                'Email encontrado. Link de recuperacao mockado enviado com sucesso.',
                $email,
                true,
            );
        }

        return new ForgotPasswordAttempt(
            ForgotPasswordOutcome::EmailNotFound,
            'Email nao encontrado na base mockada.',
            $email,
            false,
        );
    }
}
