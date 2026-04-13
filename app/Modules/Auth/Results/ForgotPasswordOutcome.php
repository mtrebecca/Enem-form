<?php

namespace App\Modules\Auth\Results;

enum ForgotPasswordOutcome: string
{
    case Sent = 'sent';
    case EmailNotFound = 'email_not_found';
}
