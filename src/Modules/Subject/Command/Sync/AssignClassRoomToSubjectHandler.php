<?php

declare(strict_types=1);

namespace App\Modules\Subject\Command\Sync;

use App\Modules\ClassRoom\Facade\ClassRoomFacade;
use App\Modules\Subject\Entity\SubjectClassRoom;
use App\Modules\Subject\Exception\ClassRoomDoesNotExist;
use App\Modules\Subject\Exception\SubjectAlreadyAssignedToClassRoom;
use App\Modules\Subject\Exception\SubjectDoesNotExist;
use App\Modules\Subject\Repository\SubjectClassRoomRepository;
use App\Modules\Subject\Repository\SubjectRepository;
use App\Shared\Command\Sync\CommandHandler;
use App\Shared\Ramsey\IdGenerator;

final class AssignClassRoomToSubjectHandler implements CommandHandler
{
    public function __construct(
        private SubjectRepository $subjectRepository,
        private ClassRoomFacade $classRoomFacade,
        private SubjectClassRoomRepository $subjectClassRoomRepository,
    ) {
    }

    public function __invoke(AssignClassRoomToSubject $command): void
    {
        if (! $this->subjectRepository->findSubjectById($command->subjectId)) {
            throw new SubjectDoesNotExist();
        }

        if (! $this->classRoomFacade->isClassRoomExistingById($command->classRoomId)) {
            throw new ClassRoomDoesNotExist();
        }

        if ($this->subjectClassRoomRepository->isSubjectAlreadyAssigned($command->subjectId, $command->classRoomId)) {
            throw new SubjectAlreadyAssignedToClassRoom();
        }

        $subjectClassRoom = new SubjectClassRoom(
            IdGenerator::generate(),
            $command->classRoomId,
            $command->subjectId,
        );

        $this->subjectClassRoomRepository->save($subjectClassRoom);
    }
}
