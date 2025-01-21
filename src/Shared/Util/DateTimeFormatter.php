<?php

declare(strict_types=1);

namespace App\Shared\Util;

use DateTimeInterface;

final class DateTimeFormatter
{
    public static function format(?DateTimeInterface $dateTime): ?string
    {
        return $dateTime?->format('d.m.Y H:i');
    }
}
