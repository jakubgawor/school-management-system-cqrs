<?php

declare(strict_types=1);

namespace App\Shared\Listener;

use App\Shared\Exception\BaseException;
use App\Shared\Request\Validator\ValidationError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

final class ExceptionListener
{
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
        } else if ($exception instanceof BaseException) {
            $code = $exception->getCode();
            $content['errors'] = $exception->getValidationKey();
        } else {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            if ($this->environment !== 'prod') {
                $content['errors'] = [
                    'server' => $exception::class . ' - ' . $event->getThrowable()->getMessage(),
                ];
            }
        }

        $event->setResponse(new JsonResponse($content ?? null, $code));
    }
}
