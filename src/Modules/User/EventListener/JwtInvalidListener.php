<?php

declare(strict_types=1);

namespace App\Modules\User\EventListener;

use App\Modules\User\Exception\InvalidJwtToken;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;

final class JwtInvalidListener
{
    public function onInvalidJwt(JWTInvalidEvent $event): void
    {
        throw new InvalidJwtToken($event->getException());
    }
}
