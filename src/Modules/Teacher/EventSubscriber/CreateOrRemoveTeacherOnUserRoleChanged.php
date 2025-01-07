<?php

declare(strict_types=1);

namespace App\Modules\Teacher\EventSubscriber;

use App\Modules\Teacher\Entity\Teacher;
use App\Modules\Teacher\Repository\TeacherRepository;
use App\Modules\User\Event\UserRoleChanged;
use App\Shared\Ramsey\IdGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CreateOrRemoveTeacherOnUserRoleChanged implements EventSubscriberInterface
{
    public function __construct(
        private TeacherRepository $teacherRepository,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserRoleChanged::class => 'onUserRoleChanged',
        ];
    }

    public function onUserRoleChanged(UserRoleChanged $event): void
    {
        if ($event->newRole === 'ROLE_TEACHER') {
            $teacher = new Teacher(IdGenerator::generate(), $event->userId);
            $this->teacherRepository->save($teacher);
        }

        if ($event->oldRole === 'ROLE_TEACHER' && $event->newRole !== 'ROLE_TEACHER') {
            $teacher = $this->teacherRepository->findByUserId($event->userId);

            if ($teacher) {
                $this->teacherRepository->remove($teacher);
            }
        }
    }
}