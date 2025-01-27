<?php

declare(strict_types=1);

namespace App\Modules\User\Query\DTO;

final readonly class UserInfo
{
    public function __construct(
        public string $id,
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $createdAt,
        public bool $isVerified,
        public bool $isActivated,
        public string $role,
        public ?string $teacherId,
        public ?string $studentId,
    ) {
    }
}
