<?php

declare(strict_types=1);

namespace App\Modules\User\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class JwtNotFoundListener
{
    public function onJwtNotFound(JwtNotFoundEvent $event): void
    {
        throw new AccessDeniedHttpException(
            'JWT not found',
            $event->getException(),
            403
        );
    }
}
