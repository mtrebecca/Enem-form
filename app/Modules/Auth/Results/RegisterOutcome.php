<?php

namespace App\Modules\Auth\Results;

enum RegisterOutcome: string
{
    case Success = 'success';
    case EmailRequired = 'email_required';
    case EmailTaken = 'email_taken';
}
