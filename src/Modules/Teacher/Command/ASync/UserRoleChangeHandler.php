<?php

declare(strict_types=1);

namespace App\Modules\Teacher\Command\ASync;

use App\Modules\Teacher\Entity\Teacher;
use App\Modules\Teacher\Repository\TeacherRepository;
use App\Modules\User\Command\ASync\UserRoleChanged;
use App\Shared\Command\Async\CommandHandler;
use App\Shared\Ramsey\IdGenerator;

final class UserRoleChangeHandler implements CommandHandler
{
    public function __construct(
        private TeacherRepository $teacherRepository,
    ) {
    }

    public function __invoke(UserRoleChanged $command): void
    {
        if ($command->newRole === 'ROLE_TEACHER') {
            $teacher = new Teacher(IdGenerator::generate(), $command->userId);
            $this->teacherRepository->save($teacher);
        }

        if ($command->oldRole === 'ROLE_TEACHER' && $command->newRole !== 'ROLE_TEACHER') {
            $teacher = $this->teacherRepository->findByUserId($command->userId);

            if ($teacher) {
                $this->teacherRepository->remove($teacher);
            }
        }
    }
}
