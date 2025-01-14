<?php

declare(strict_types=1);

namespace App\Modules\Teacher\Facade;

use App\Modules\Teacher\Repository\TeacherRepository;

final class TeacherFacade
{
    public function __construct(
        private TeacherRepository $teacherRepository,
    ) {
    }

    public function existsTeacherById(string $teacherId): bool
    {
        return (bool) $this->teacherRepository->findById($teacherId);
    }
}
