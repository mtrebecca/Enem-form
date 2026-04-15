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
use Illuminate\Support\Str;

class AuthService
{
    public function register(array $payload): RegisterAttempt
    {
        $email = (string) ($payload['email'] ?? '');
        $senha = (string) ($payload['senha'] ?? '');

        if (User::query()->where('email', $email)->exists()) {
            return new RegisterAttempt(
                RegisterOutcome::EmailTaken,
                'Este email ja esta cadastrado.',
            );
        }

        $user = User::query()->create([
            'name' => (string) ($payload['nome'] ?? 'Novo Usuario'),
            'email' => $email,
            'password' => Hash::make($senha),
        ]);
        $token = $this->issueApiToken($user);

        return new RegisterAttempt(
            RegisterOutcome::Success,
            'Usuario cadastrado com sucesso',
            $token,
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
        $token = $this->issueApiToken($user);

        return new LoginAttempt(
            LoginOutcome::Success,
            'Login realizado com sucesso',
            $token,
            [
                'id' => $user->id,
                'nome' => $user->name,
                'email' => $user->email,
            ],
        );
    }

    public function logout(?User $user): array
    {
        if ($user) {
            $user->forceFill(['api_token' => null])->save();
        }

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

    private function issueApiToken(User $user): string
    {
        $plainToken = Str::random(60);
        $user->forceFill(['api_token' => hash('sha256', $plainToken)])->save();

        return $plainToken;
    }
}
