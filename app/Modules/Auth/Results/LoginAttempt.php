<?php

namespace App\Modules\Auth\Results;

final readonly class LoginAttempt
{
    public function __construct(
        public LoginOutcome $outcome,
        public string $message,
        public ?string $token = null,
        public ?array $user = null,
    ) {}

    public function toResponseBody(): array
    {
        $body = ['message' => $this->message];
        if ($this->token !== null) {
            $body['token'] = $this->token;
        }
        if ($this->user !== null) {
            $body['user'] = $this->user;
        }

        return $body;
    }
}
