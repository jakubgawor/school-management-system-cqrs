<?php

declare(strict_types=1);

namespace App\Modules\Subject\Command\Sync;

use App\Modules\Subject\Entity\Subject;
use App\Modules\Subject\Repository\SubjectRepository;
use App\Shared\Command\Sync\CommandHandler;
use App\Shared\Ramsey\IdGenerator;

class CreateSubjectHandler implements CommandHandler
{
    public function __construct(
        private SubjectREpository $subjectRepository,
    ) {
    }

    public function __invoke(CreateSubject $command): void
    {
        $subject = new Subject(
            IdGenerator::generate(),
            $command->teacherId,
            $command->name,
            $command->description,
        );

        $this->subjectRepository->save($subject);
    }
}
