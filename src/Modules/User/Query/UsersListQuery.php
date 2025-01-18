<?php

declare(strict_types=1);

namespace App\Modules\User\Query;

use App\Modules\User\Entity\User;
use App\Modules\User\Query\Result\UserInfo as UserInfoDTO;
use App\Modules\User\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RequestStack;

final class UsersListQuery
{
    public function __construct(
        private UserRepository $userRepository,
        private RequestStack $requestStack,
    ) {
    }

    public function execute(): array
    {
        $request = $this->requestStack->getCurrentRequest();

        $page = max(1, $request->query->getInt('page', 1));
        $limit = max(1, $request->query->getInt('limit', 10));
        if ($limit > 50) {
            $limit = 50;
        }

        $users = $this->userRepository->findPaginatedUsers($page, $limit);
        $totalCount = count($users);

        $data = [];
        /** @var User $user */
        foreach ($users as $user) {
            $data[] = new UserInfoDTO(
                $user->getId(),
                $user->getFirstName(),
                $user->getLastName(),
                $user->getEmail(),
                $user->getCreatedAt(),
                $user->isVerified(),
                $user->isActivated(),
                $user->getRoles()[0]
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
