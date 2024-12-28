<?php

declare(strict_types=1);

namespace App\Modules\User\Controller;

use App\Modules\User\Exception\TokenDoesNotExists;
use App\Modules\User\Exception\TokenExpired;
use App\Modules\User\Exception\UserAlreadyExists;
use App\Modules\User\Request\V1\UserRegister as UserRegisterRequestV1;
use App\Modules\User\Request\V1\VerifyEmail as VerifyEmailRequestV1;
use App\Shared\Command\Sync\CommandBus as SyncCommandBus;
use App\Shared\Request\Validator\RequestValidator;
use App\Shared\Request\Validator\ValidationError;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\RequestBody;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    public function __construct(
        private RequestValidator $validator,
        private SyncCommandBus $syncCommandBus,
    ) {
    }

    #[Post(
        summary: 'Register a new user',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                required: ['email', 'password'],
                properties: [
                    new Property(property: 'email', description: 'Email address', type: 'string', format: 'email'),
                    new Property(property: 'password', description: 'Password', type: 'string'),
                ],
                type: 'object'
            )
        ),
        tags: ['User', 'v1']
    )]
    #[Route('/api/v1/user/register', name: 'v1.user.register', methods: ['POST'])]
    public function registerV1(UserRegisterRequestV1 $request): Response
    {
        $this->validator->validate($request);

        try {
            $this->syncCommandBus->dispatch($request->toCommand());
        } catch (UserAlreadyExists) {
            throw new ValidationError([
                ValidationError::GENERAL => ['VALIDATION.USER_ALREADY_EXISTS'],
            ]);
        }

        return new JsonResponse(null, Response::HTTP_CREATED);
    }

    #[Post(
        summary: 'Verify user email with a token',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                required: ['token'],
                properties: [
                    new Property(property: 'token', description: 'Verification token', type: 'string'),
                ],
                type: 'object'
            )
        ),
        tags: ['User', 'v1']
    )]
    #[Route('/api/v1/user/verify_email', name: 'v1.user.verify_email', methods: ['POST'])]
    public function verifyEmailV1(VerifyEmailRequestV1 $request): Response
    {
        $this->validator->validate($request);

        try {
            $this->syncCommandBus->dispatch($request->toCommand());
        } catch (TokenExpired) {
            throw new ValidationError([
                ValidationError::GENERAL => ['VALIDATION.TOKEN_EXPIRED'],
            ]);
        } catch (TokenDoesNotExists) {
            throw new ValidationError([
                ValidationError::GENERAL => ['VALIDATION.TOKEN_NOT_FOUND'],
            ]);
        }

        return new JsonResponse([
            'status' => 'ok',
        ], Response::HTTP_OK);
    }
}
