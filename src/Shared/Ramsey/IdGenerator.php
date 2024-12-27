<?php

declare(strict_types=1);

namespace App\Shared\Ramsey;

use Symfony\Component\Uid\Uuid;

final class IdGenerator
{
    public static function generate(): string
    {
        return Uuid::v7()->toString();
    }
}
