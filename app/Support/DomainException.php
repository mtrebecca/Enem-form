<?php

namespace App\Support;

use RuntimeException;

class DomainException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly int $status = 422,
        public readonly string $codeName = 'DOMAIN_ERROR',
        public readonly array $details = [],
    ) {
        parent::__construct($message);
    }
}
