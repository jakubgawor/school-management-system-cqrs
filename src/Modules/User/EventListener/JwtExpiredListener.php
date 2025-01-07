<?php

declare(strict_types=1);

namespace App\Modules\User\EventListener;

use App\Modules\User\Exception\ExpiredJwtToken;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;

final class JwtExpiredListener
{
    public function onExpiredJwt(JWTExpiredEvent $event): void
    {
        throw new ExpiredJwtToken($event->getException());
    }
}
