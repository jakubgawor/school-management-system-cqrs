<?php

declare(strict_types=1);

namespace App\Modules\Student\Query;

use App\Modules\Student\Repository\StudentRepository;

final class StudentDetailsQuery
{
    public function __construct(
        private StudentRepository $studentRepository,
    ) {
    }

    public function execute(string $studentId): array
    {
        return $this->studentRepository->getStudentDetailsWithSubjectsAndTeacher($studentId);
    }
}
