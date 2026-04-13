<?php

namespace App\Modules\Auth\Results;

final readonly class ForgotPasswordAttempt
{
    public function __construct(
        public ForgotPasswordOutcome $outcome,
        public string $message,
        public string $email,
        public bool $sent,
    ) {}

    public function toResponseBody(): array
    {
        return [
            'message' => $this->message,
            'email' => $this->email,
            'sent' => $this->sent,
        ];
    }
}
