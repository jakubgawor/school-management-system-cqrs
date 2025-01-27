<?php

declare(strict_types=1);

namespace App\Modules\Grade\Service;

final class GradeWeightedAverageService
{
    public function count(array $grades): float
    {
        $sum = 0;
        $weightSum = 0;
        foreach ($grades as $grade) {
            $gradeValue = (float) $grade['grade']->value;

            $sum += $gradeValue * $grade['weight'];
            $weightSum += $grade['weight'];
        }

        $weightedAvg = 0;
        if ($weightSum > 0) {
            $weightedAvg = $sum / $weightSum;
        }

        return round($weightedAvg, 2);
    }
}
