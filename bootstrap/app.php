<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use App\Support\DomainException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware()
    ->withExceptions(function (Exceptions $exceptions): void {
        $jsonError = static function (
            int $status,
            string $code,
            string $message,
            array $details = []
        ): JsonResponse {
            return response()->json([
                'message' => $message,
                'error' => [
                    'code' => $code,
                    'message' => $message,
                    'details' => $details,
                ],
            ], $status);
        };

        $exceptions->render(function (ValidationException $e) use ($jsonError): JsonResponse {
            return $jsonError(
                422,
                'VALIDATION_ERROR',
                'Dados invalidos.',
                ['fields' => $e->errors()],
            );
        });

        $exceptions->render(function (AuthenticationException $e) use ($jsonError): JsonResponse {
            $message = trim((string) $e->getMessage()) !== '' ? $e->getMessage() : 'Nao autenticado.';

            return $jsonError(401, 'HTTP_401', $message);
        });

        $exceptions->render(function (DomainException $e) use ($jsonError): JsonResponse {
            return $jsonError(
                $e->status,
                $e->codeName,
                trim((string) $e->getMessage()) !== '' ? $e->getMessage() : 'Erro de dominio.',
                $e->details,
            );
        });

        $exceptions->render(function (HttpExceptionInterface $e) use ($jsonError): JsonResponse {
            $status = $e->getStatusCode();
            $message = trim((string) $e->getMessage()) !== '' ? $e->getMessage() : 'Erro na requisicao.';

            return $jsonError($status, "HTTP_{$status}", $message);
        });

        $exceptions->render(function (\Throwable $e) use ($jsonError): JsonResponse {
            if ($e instanceof HttpResponseException) {
                throw $e;
            }

            return $jsonError(500, 'INTERNAL_ERROR', 'Erro interno no servidor.');
        });
    })
    ->create();
