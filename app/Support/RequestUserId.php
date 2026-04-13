<?php

namespace App\Support;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class RequestUserId
{
    public static function require(Request $request): int
    {
        $raw = $request->header('X-User-Id', '');
        $id = is_numeric($raw) ? (int) $raw : 0;

        if ($id < 1) {
            throw new HttpException(401, 'Envie o header X-User-Id com o id do usuario autenticado.');
        }

        return $id;
    }
}
