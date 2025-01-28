<?php

declare(strict_types=1);

namespace App\Modules\User\Query;

use App\Modules\Student\Facade\StudentFacade;
use App\Modules\Teacher\Facade\TeacherFacade;
use App\Modules\User\Entity\User;
use App\Modules\User\Query\DTO\UserInfo as UserInfoDTO;
use App\Modules\User\Repository\UserRepository;
use App\Shared\Util\DateTimeFormatter;
use Symfony\Component\HttpFoundation\RequestStack;

final class UsersListQuery
{
    public function __construct(
        private UserRepository $userRepository,
        private RequestStack $requestStack,
        private TeacherFacade $teacherFacade,
        private StudentFacade $studentFacade,
    ) {
    }

    public function execute(): array
    {
        $request = $this->requestStack->getCurrentRequest();

        $searchPhrase = $request->get('searchPhrase');
        $page = max(1, $request->query->getInt('page', 1));
        $limit = max(1, $request->query->getInt('limit', 10));
        if ($limit > 50) {
            $limit = 50;
        }

        $users = $this->userRepository->findPaginatedUsers($page, $limit, $searchPhrase);
        $totalCount = $this->userRepository->getCountOfPaginatedUsers($searchPhrase);

        $userIds = array_map(fn (User $user) => $user->getId(), $users);
        $teachersMap = $this->teacherFacade->findTeacherIdsByUserIds($userIds);
        $studentsMap = $this->studentFacade->findStudentIdsByUserIds($userIds);

        $data = [];
        /** @var User $user */
        foreach ($users as $user) {
            $userId = $user->getId();

            $teacherId = $teachersMap[$userId] ?? null;
            $studentId = $studentsMap[$userId] ?? null;

            $data[] = new UserInfoDTO(
                $userId,
                $user->getFirstName(),
                $user->getLastName(),
                $user->getEmail(),
                DateTimeFormatter::format($user->getCreatedAt()),
                $user->isVerified(),
                $user->isActivated(),
                $user->getRoles()[0],
                $teacherId,
                $studentId
            );
        }

        return [
            'page' => $page,
            'limit' => $limit,
            'total' => $totalCount,
            'totalPages' => ceil($totalCount / $limit),
            'data' => $data,
        ];
    }
}
