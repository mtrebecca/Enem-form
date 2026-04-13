<?php

namespace Tests;

use Database\Seeders\EnemDemoSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

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
        return ['X-User-Id' => (string) $userId];
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
