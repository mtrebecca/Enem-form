<?php

namespace App\Modules\Auth\Results;

enum LoginOutcome: string
{
    case Success = 'success';
    case InvalidCredentials = 'invalid_credentials';
}
