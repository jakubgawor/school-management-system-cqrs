<?php

declare(strict_types=1);

namespace App\Modules\User\Query;

use App\Modules\User\Query\Result\UserBasicInfo as UserBasicInfoDTO;
use Symfony\Component\Security\Core\User\UserInterface;

final class GetUserBasicInfoQuery
{
    public function execute(UserInterface $user): UserBasicInfoDTO
    {
        return new UserBasicInfoDTO(
            $user->getId(),
            $user->getEmail(),
            $user->getRoles(),
        );
    }
}
