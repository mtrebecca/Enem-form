<?php

namespace Tests;

use App\Modules\Users\Models\User;
use Database\Seeders\EnemDemoSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    public const USUARIO_DEMO_ID = 1;

    public const USUARIO_DEMO_EMAIL = 'maria@enem.dev';

    public const USUARIO_DEMO_SENHA = '123456';

    final protected function popularBaseComDemoEnem(): void
    {
        $this->seed(EnemDemoSeeder::class);
    }

    final protected function cabecalhosUsuario(int $userId = self::USUARIO_DEMO_ID): array
    {
        $user = User::query()->findOrFail($userId);
        $token = Str::random(60);
        $user->forceFill(['api_token' => hash('sha256', $token)])->save();

        return ['Authorization' => "Bearer {$token}"];
    }

    final protected static function indicePrimeiraOpcaoCorreta(?array $opcoes): ?int
    {
        if ($opcoes === null) {
            return null;
        }

        foreach ($opcoes as $indice => $opcao) {
            if (! empty($opcao['correta'])) {
                return $indice;
            }
        }

        return null;
    }
}
