<?php

declare(strict_types=1);

namespace App\Modules\User\Controller;

use App\Modules\User\Exception\RoleAlreadyAssigned;
use App\Modules\User\Exception\TokenCooldownViolation;
use App\Modules\User\Exception\TokenDoesNotExists;
use App\Modules\User\Exception\TokenExpired;
use App\Modules\User\Exception\UserAlreadyExists;
use App\Modules\User\Exception\UserNotFound;
use App\Modules\User\Query\GetUserBasicInfoQuery;
use App\Modules\User\Request\V1\ChangeForgottenPassword as ChangeForgottenPasswordRequestV1;
use App\Modules\User\Request\V1\ChangeUserRole as ChangeUserRoleRequestV1;
use App\Modules\User\Request\V1\RequestPasswordChange as RequestPasswordChangeRequestV1;
use App\Modules\User\Request\V1\ResendVerificationCode as ResendVerificationCodeRequestV1;
use App\Modules\User\Request\V1\UserRegister as UserRegisterRequestV1;
use App\Modules\User\Request\V1\VerifyEmail as VerifyEmailRequestV1;
use App\Shared\Command\Sync\CommandBus as SyncCommandBus;
use App\Shared\Request\Validator\RequestValidator;
use App\Shared\Request\Validator\ValidationError;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response as OAResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Event\LogoutEvent;

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
                required: ['firstName', 'lastName', 'email', 'password', 'confirmPassword'],
                properties: [
                    new Property(property: 'firstName', description: 'First name', type: 'string'),
                    new Property(property: 'lastName', description: 'Last name', type: 'string'),
                    new Property(property: 'email', description: 'Email address', type: 'string', format: 'email'),
                    new Property(property: 'password', description: 'Password', type: 'string'),
                    new Property(property: 'confirmPassword', description: 'Confirm password', type: 'string'),
                ],
                type: 'object'
            )
        ),
        tags: ['User', 'v1']
    )]
    #[IsGranted('UNAUTHENTICATED_USER')]
    #[Route('/api/v1/user/register', name: 'v1.user.register', methods: ['POST'])]
    public function registerV1(UserRegisterRequestV1 $request): Response
    {
        $this->validator->validate($request);

        try {
            $this->syncCommandBus->dispatch($request->toCommand());
        } catch (UserAlreadyExists $exception) {
            throw new ValidationError([
                ValidationError::VALIDATION => [$exception->getValidationKey()],
            ]);
        }

        return new JsonResponse([
            'status' => 'ok',
        ], Response::HTTP_CREATED);
    }

    #[Post(
        summary: 'User login',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                required: ['username', 'password'],
                properties: [
                    new Property(property: 'username', description: 'User email address', type: 'string', format: 'email'),
                    new Property(property: 'password', description: 'User password', type: 'string'),
                ],
                type: 'object'
            )
        ),
        tags: ['User', 'v1'],
    )]
    #[Route('/api/v1/user/login', methods: ['POST'])]
    public function login(): void
    {
    }

    #[Post(
        summary: 'User logout',
        tags: ['User', 'v1'],
    )]
    #[Security(name: 'Bearer')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/api/v1/user/logout', name: 'v1.user.logout', methods: ['POST'])]
    public function logout(
        Request $request,
        EventDispatcherInterface $eventDispatcher,
        TokenStorageInterface $tokenStorage,
    ): Response {
        $eventDispatcher->dispatch(new LogoutEvent($request, $tokenStorage->getToken()));

        return new JsonResponse([
            'status' => 'ok',
        ], Response::HTTP_OK);
    }

    #[Post(
        summary: 'Verify user email with a token',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                required: ['email', 'token'],
                properties: [
                    new Property(property: 'email', description: 'Email address', type: 'string', format: 'email'),
                    new Property(property: 'token', description: 'Verification token', type: 'string'),
                ],
                type: 'object'
            )
        ),
        tags: ['User', 'v1']
    )]
    #[IsGranted('IS_NOT_VERIFIED_BY_EMAIL')]
    #[Route('/api/v1/user/verify_email', name: 'v1.user.verify_email', methods: ['POST'])]
    public function verifyEmailV1(VerifyEmailRequestV1 $request): Response
    {
        $this->validator->validate($request);

        try {
            $this->syncCommandBus->dispatch($request->toCommand());
        } catch (TokenExpired|TokenDoesNotExists|UserNotFound $exception) {
            throw new ValidationError([
                ValidationError::VALIDATION => [$exception->getValidationKey()],
            ]);
        }

        return new JsonResponse([
            'status' => 'ok',
        ], Response::HTTP_OK);
    }

    #[Post(
        summary: 'Resend verification code',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                required: ['email', 'token'],
                properties: [
                    new Property(
                        property: 'email',
                        description: 'Email address',
                        type: 'string',
                        format: 'email'
                    ),
                    new Property(
                        property: 'type',
                        description: 'Type of token (enum)',
                        type: 'string',
                        enum: ['email_verification', 'password_reset']
                    ),
                ],
                type: 'object'
            )
        ),
        tags: ['User', 'v1']
    )]
    #[Route('/api/v1/user/resend_verification_code', name: 'v1.user.resend_verification_code', methods: ['POST'])]
    public function resendVerificationCodeV1(ResendVerificationCodeRequestV1 $request): Response
    {
        $this->validator->validate($request);

        try {
            $this->syncCommandBus->dispatch($request->toCommand());
        } catch (UserNotFound|TokenCooldownViolation $exception) {
            throw new ValidationError([
                ValidationError::VALIDATION => [$exception->getValidationKey()],
            ]);
        }

        return new JsonResponse([
            'status' => 'ok',
        ], Response::HTTP_OK);
    }

    #[Post(
        summary: 'Request password change',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                required: ['email'],
                properties: [
                    new Property(property: 'email', description: 'Email address', type: 'string', format: 'email'),
                ],
                type: 'object'
            )
        ),
        tags: ['User', 'v1']
    )]
    #[IsGranted('UNAUTHENTICATED_USER')]
    #[Route('/api/v1/user/request_password_change', name: 'v1.user.request_password_change', methods: ['POST'])]
    public function requestPasswordChangeV1(RequestPasswordChangeRequestV1 $request): Response
    {
        $this->validator->validate($request);

        try {
            $this->syncCommandBus->dispatch($request->toCommand());
        } catch (UserNotFound $exception) {
            throw new ValidationError([
                ValidationError::VALIDATION => [$exception->getValidationKey()],
            ]);
        }

        return new JsonResponse([
            'status' => 'ok',
        ], Response::HTTP_OK);
    }

    #[Post(
        summary: 'Change forgotten password',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                required: ['email', 'password', 'token'],
                properties: [
                    new Property(property: 'email', description: 'Email address', type: 'string', format: 'email'),
                    new Property(property: 'password', description: 'New password', type: 'string', format: 'email'),
                    new Property(property: 'token', description: 'Verification token', type: 'string'),
                ],
                type: 'object'
            )
        ),
        tags: ['User', 'v1']
    )]
    #[IsGranted('UNAUTHENTICATED_USER')]
    #[Route('/api/v1/user/change_forgotten_password', name: 'v1.user.change_forgotten_password', methods: ['POST'])]
    public function changeForgottenPasswordV1(ChangeForgottenPasswordRequestV1 $request): Response
    {
        $this->validator->validate($request);

        try {
            $this->syncCommandBus->dispatch($request->toCommand());
        } catch (UserNotFound|TokenDoesNotExists|TokenExpired $exception) {
            throw new ValidationError([
                ValidationError::VALIDATION => [$exception->getValidationKey()],
            ]);
        }

        return new JsonResponse([
            'status' => 'ok',
        ], Response::HTTP_OK);
    }

    #[Get(
        summary: 'Get basic user info',
        tags: ['User', 'v1'],
        responses: [
            new OAResponse(
                response: 200,
                description: 'Returns the basic user info',
                content: new JsonContent(
                    properties: [
                        new Property(
                            property: 'id',
                            description: 'Unique user identifier',
                            type: 'string',
                            format: 'uuid'
                        ),
                        new Property(
                            property: 'firstName',
                            description: 'User first name',
                            type: 'string',
                        ),
                        new Property(
                            property: 'lastName',
                            description: 'User last name',
                            type: 'string',
                        ),
                        new Property(
                            property: 'email',
                            description: 'User email address',
                            type: 'string',
                            format: 'email'
                        ),
                        new Property(
                            property: 'roles',
                            description: 'Array of user roles',
                            type: 'array',
                            items: new Items(
                                type: 'string'
                            )
                        ),
                    ],
                    type: 'object'
                ),
            ),
            new OAResponse(
                response: 401,
                description: 'Unauthorized access',
            ),
        ]
    )]
    #[Security(name: 'Bearer')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/api/v1/user/me', name: 'v1.user.me', methods: ['GET'])]
    public function meV1(GetUserBasicInfoQuery $getUserBasicInfoQuery): Response
    {
        return new JsonResponse($getUserBasicInfoQuery->execute($this->getUser()->getId()));
    }

    #[Post(
        summary: 'Change user role',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                required: ['role'],
                properties: [
                    new Property(
                        property: 'role',
                        description: 'User role, eg. ROLE_USER/ROLE_STUDENT/ROLE_TEACHER/ROLE_ADMIN',
                        type: 'string',
                    ),
                ],
                type: 'object'
            )
        ),
        tags: ['User', 'v1']
    )]
    #[Security(name: 'Bearer')]
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/v1/user/{userId}/change_role', name: 'v1.user.change_role', methods: ['POST'])]
    public function changeUserRoleV1(string $userId, ChangeUserRoleRequestV1 $request): Response
    {
        $request->id = $userId;

        $this->validator->validate($request);

        try {
            $this->syncCommandBus->dispatch($request->toCommand());
        } catch (UserNotFound|RoleAlreadyAssigned $exception) {
            throw new ValidationError([
                ValidationError::VALIDATION => [$exception->getValidationKey()],
            ]);
        }

        return new JsonResponse([
            'status' => 'ok',
        ], Response::HTTP_OK);
    }
}
