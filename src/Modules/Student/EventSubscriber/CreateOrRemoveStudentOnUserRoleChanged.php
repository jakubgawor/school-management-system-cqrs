<?php

declare(strict_types=1);

namespace App\Modules\Student\EventSubscriber;

use App\Modules\Student\Entity\Student;
use App\Modules\Student\Repository\StudentRepository;
use App\Modules\User\Event\UserRoleChanged;
use App\Shared\Ramsey\IdGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CreateOrRemoveStudentOnUserRoleChanged implements EventSubscriberInterface
{
    public function __construct(
        private StudentRepository $studentRepository,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            UserRoleChanged::class => 'onUserRoleChanged',
        ];
    }

    public function onUserRoleChanged(UserRoleChanged $event): void
    {
        if ($event->newRole === 'ROLE_STUDENT') {
            $student = new Student(IdGenerator::generate(), $event->userId);
            $this->studentRepository->save($student);
        }

        if ($event->oldRole === 'ROLE_STUDENT' && $event->newRole === 'ROLE_USER') {
            $student = $this->studentRepository->findByUserId($event->userId);

            if ($student) {
                $this->studentRepository->remove($student);
            }
        }
    }
}