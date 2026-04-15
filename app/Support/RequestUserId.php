<?php

namespace App\Support;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class RequestUserId
{
    public static function require(Request $request): int
    {
        $id = (int) ($request->user()?->id ?? 0);

        if ($id < 1) {
            throw new HttpException(401, 'Token de autenticacao invalido ou ausente.');
        }

        return $id;
    }
}
