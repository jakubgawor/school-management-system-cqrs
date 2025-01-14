<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Facade;

use App\Modules\ClassRoom\Repository\ClassRoomRepository;

final class ClassRoomFacade
{
    public function __construct(
        private ClassRoomRepository $classRoomRepository,
    ) {
    }

    public function isClassRoomExistingById(string $id): bool
    {
        return (bool) $this->classRoomRepository->findById($id);
    }
}
