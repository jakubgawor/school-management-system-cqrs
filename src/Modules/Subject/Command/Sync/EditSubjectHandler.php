<?php

declare(strict_types=1);

namespace App\Modules\Subject\Command\Sync;

use App\Modules\Subject\Exception\SubjectDoesNotExist;
use App\Modules\Subject\Repository\SubjectRepository;
use App\Shared\Command\Sync\CommandHandler;

final class EditSubjectHandler implements CommandHandler
{
    public function __construct(
        private SubjectRepository $subjectRepository,
    ) {
    }

    public function __invoke(EditSubject $command): void
    {
        $subject = $this->subjectRepository->findSubjectById($command->subjectId);
        if (! $subject) {
            throw new SubjectDoesNotExist();
        }

        $subject
            ->setTeacherId($command->teacherId)
            ->setName($command->name)
            ->setDescription($command->description);

        $this->subjectRepository->save($subject);
    }
}
