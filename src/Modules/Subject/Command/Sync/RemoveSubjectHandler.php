<?php

declare(strict_types=1);

namespace App\Modules\Subject\Command\Sync;

use App\Modules\Subject\Exception\SubjectDoesNotExist;
use App\Modules\Subject\Repository\SubjectClassRoomRepository;
use App\Modules\Subject\Repository\SubjectRepository;
use App\Shared\Command\Sync\CommandHandler;

final class RemoveSubjectHandler implements CommandHandler
{
    public function __construct(
        private SubjectRepository $subjectRepository,
        private SubjectClassRoomRepository $subjectClassRoomRepository
    ) {
    }

    public function __invoke(RemoveSubject $command): void
    {
        $subject = $this->subjectRepository->findSubjectById($command->subjectId);
        if (! $subject) {
            throw new SubjectDoesNotExist();
        }

        $classRoomsAssignedToSubject = $this->subjectClassRoomRepository->getClassRoomsAssignedToSubject($command->subjectId);
        foreach ($classRoomsAssignedToSubject as $classRoom) {
            $this->subjectClassRoomRepository->remove($classRoom);
        }

        $this->subjectRepository->remove($subject);
    }
}
