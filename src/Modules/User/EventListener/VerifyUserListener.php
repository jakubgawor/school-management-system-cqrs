<?php

declare(strict_types=1);

namespace App\Modules\User\EventListener;

use App\Modules\User\Exception\UserIsNotVerified;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final class VerifyUserListener
{
    private const array ALLOWED_ROUTES = [
        'v1.user.verify_email',
        'v1.user.resend_verification_code',
        'v1.user.logout',
        'v1.user.me',
        'v1.user.change_email',
    ];

    public function __construct(
        private Security $security,
    ) {
    }

    public function isUserVerified(RequestEvent $event): void
    {
        if (in_array($event->getRequest()->attributes->get('_route'), self::ALLOWED_ROUTES, true)) {
            return;
        }

        $user = $this->security->getUser();

        if ($user && ! $user->isVerified()) {
            throw new UserIsNotVerified();
        }
    }
}
