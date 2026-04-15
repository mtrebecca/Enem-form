<?php

namespace App\Modules\Auth\Results;

enum RegisterOutcome: string
{
    case Success = 'success';
    case EmailTaken = 'email_taken';
}
