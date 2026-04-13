<?php

namespace App\Modules\Auth\Results;

final readonly class RegisterAttempt
{
    public function __construct(
        public RegisterOutcome $outcome,
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
