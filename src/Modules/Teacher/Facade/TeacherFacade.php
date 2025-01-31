<?php

declare(strict_types=1);

namespace App\Modules\Teacher\Facade;

use App\Modules\Teacher\Entity\Teacher;
use App\Modules\Teacher\Repository\TeacherRepository;
use JetBrains\PhpStorm\ArrayShape;

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

    #[ArrayShape([
        'userId' => 'teacherId',
    ])]
    public function findTeacherIdsByUserIds(array $userIds): array
    {
        $map = [];
        foreach ($this->teacherRepository->findByUserIds($userIds) as $teacher) {
            $map[$teacher->getUserId()] = $teacher->getId();
        }

        return $map;
    }

    public function findTeacherByUserId(string $userId): Teacher
    {
        return $this->teacherRepository->findByUserId($userId);
    }
}
