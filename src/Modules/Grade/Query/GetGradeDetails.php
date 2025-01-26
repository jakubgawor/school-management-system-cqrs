<?php

declare(strict_types=1);

namespace App\Modules\Grade\Query;

use App\Modules\Grade\Query\DTO\GradeDetails;
use App\Modules\Grade\Repository\GradeRepository;
use App\Shared\Util\DateTimeFormatter;

final class GetGradeDetails
{
    public function __construct(
        private GradeRepository $gradeRepository,
    ) {
    }

    public function execute(string $gradeId): GradeDetails
    {
        $gradeDetails = $this->gradeRepository->getGradeDetails($gradeId);

        return new GradeDetails(
            $gradeDetails['id'],
            $gradeDetails['description'],
            $gradeDetails['weight'],
            DateTimeFormatter::format($gradeDetails['createdAt']),
            DateTimeFormatter::format($gradeDetails['updatedAt']),
            $gradeDetails['firstName'],
            $gradeDetails['lastName'],
            $gradeDetails['email'],
        );
    }
}
