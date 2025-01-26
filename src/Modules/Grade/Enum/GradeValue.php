<?php

declare(strict_types=1);

namespace App\Modules\Grade\Enum;

enum GradeValue: string
{
    case ONE = '1';
    case ONE_PLUS = '1.5';

    case TWO_MINUS = '1.75';
    case TWO = '2';
    case TWO_PLUS = '2.5';

    case THREE_MINUS = '2.75';
    case THREE = '3';
    case THREE_PLUS = '3.5';

    case FOUR_MINUS = '3.75';
    case FOUR = '4';
    case FOUR_PLUS = '4.5';

    case FIVE_MINUS = '4.75';
    case FIVE = '5';
    case FIVE_PLUS = '5.5';

    case SIX_MINUS = '5.75';
    case SIX = '6';

    public static function list(): array
    {
        $list = [];
        foreach (self::cases() as $grade) {
            $list[] = $grade->value;
        }

        return $list;
    }
}
