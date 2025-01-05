<?php

declare(strict_types=1);

namespace App\Modules\User\Query\Result;

final readonly class UserBasicInfo
{
    public function __construct(
        public string $id,
        public string $email,
        public array $roles,
    ) {
    }
}
