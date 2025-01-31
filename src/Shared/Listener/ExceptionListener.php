<?php

declare(strict_types=1);

namespace App\Shared\Listener;

use App\Shared\Exception\BaseException;
use App\Shared\Request\Validator\ValidationError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class ExceptionListener
{
    public const string GENERAL = 'general';

    public function __construct(
        private string $environment,
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ValidationError) {
            $code = Response::HTTP_BAD_REQUEST;
            $content['errors'] = $exception->getErrors();
        } elseif ($exception instanceof BaseException) {
            $code = $exception->getCode();
            $content['errors'] = $exception->getValidationKey();
        } elseif ($exception instanceof AccessDeniedHttpException) {
            $code = Response::HTTP_FORBIDDEN;
            $content['errors'] = 'VALIDATION.ACCESS_DENIED';
        } else {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            if ($this->environment !== 'prod') {
                $content['errors'] = [
                    'server' => $exception::class . ' - ' . $event->getThrowable()->getMessage(),
                ];
            } else {
                $content['errors'] = [
                    self::GENERAL => 'INTERNAL_SERVER_ERROR',
                ];
            }

        }

        $event->setResponse(new JsonResponse($content ?? null, $code));
    }
}
