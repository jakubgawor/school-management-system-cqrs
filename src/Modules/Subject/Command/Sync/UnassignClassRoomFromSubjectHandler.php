<?php

declare(strict_types=1);

namespace App\Modules\Subject\Command\Sync;

use App\Modules\Subject\Exception\AssignationNotFound;
use App\Modules\Subject\Repository\SubjectClassRoomRepository;
use App\Shared\Command\Sync\CommandHandler;

final class UnassignClassRoomFromSubjectHandler implements CommandHandler
{
    public function __construct(
        private SubjectClassRoomRepository $subjectClassRoomRepository,
    ) {
    }

    public function __invoke(UnassignClassRoomFromSubject $command): void
    {
        $assignation = $this->subjectClassRoomRepository->getSubjectAssignation(
            $command->subjectId,
            $command->classRoomId
        );

        if (! $assignation) {
            throw new AssignationNotFound();
        }

        $this->subjectClassRoomRepository->remove($assignation);
    }
}
