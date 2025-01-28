<?php

declare(strict_types=1);

namespace App\Modules\Student\Command\ASync;

use App\Modules\Student\Entity\Student;
use App\Modules\Student\Repository\StudentRepository;
use App\Modules\User\Command\ASync\UserRoleChanged;
use App\Shared\Command\Async\CommandHandler;
use App\Shared\Ramsey\IdGenerator;

final class UserRoleChangeHandler implements CommandHandler
{
    public function __construct(
        private StudentRepository $studentRepository,
    ) {
    }

    public function __invoke(UserRoleChanged $command): void
    {
        if ($command->newRole === 'ROLE_STUDENT') {
            $student = new Student(IdGenerator::generate(), $command->userId);
            $this->studentRepository->save($student);
        }

        if ($command->oldRole === 'ROLE_STUDENT' && $command->newRole !== 'ROLE_STUDENT') {
            $student = $this->studentRepository->findByUserId($command->userId);

            if ($student) {
                $this->studentRepository->remove($student);
            }
        }
    }
}
