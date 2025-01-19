<?php

declare(strict_types=1);

namespace App\Modules\User\Query\DTO;

final readonly class UserBasicInfo
{
    public function __construct(
        public string $id,
        public string $firstName,
        public string $lastName,
        public string $email,
        public array $roles,
        public bool $isActivated,
    ) {
    }
}