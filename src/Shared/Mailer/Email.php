<?php

declare(strict_types=1);

namespace App\Shared\Mailer;

final readonly class Email
{
    public function __construct(
        public string $recipient,
        public string $subject,
        public string $text,
    ) {
    }
}
