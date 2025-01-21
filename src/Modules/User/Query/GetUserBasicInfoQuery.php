<?php

declare(strict_types=1);

namespace App\Modules\User\Query;

use App\Modules\User\Query\DTO\UserBasicInfo as UserBasicInfoDTO;
use App\Modules\User\Repository\UserRepository;

final class GetUserBasicInfoQuery
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function execute(string $id): UserBasicInfoDTO
    {
        $user = $this->userRepository->findById($id);

        return new UserBasicInfoDTO(
            $user->getId(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getEmail(),
            $user->getRoles(),
            $user->isActivated(),
            $user->isVerified(),
        );
    }
}
