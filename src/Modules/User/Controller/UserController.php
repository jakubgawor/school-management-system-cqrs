<?php

declare(strict_types=1);

namespace App\Modules\User\Controller;

use App\Modules\User\Exception\CannotChangeOwnActivation;
use App\Modules\User\Exception\EmailAlreadyUsed;
use App\Modules\User\Exception\PasswordsDoNotMatch;
use App\Modules\User\Exception\TokenCooldownViolation;
use App\Modules\User\Exception\TokenDoesNotExists;
use App\Modules\User\Exception\TokenExpired;
use App\Modules\User\Exception\UserAlreadyExists;
use App\Modules\User\Exception\UserNotFound;
use App\Modules\User\Query\GetUserBasicInfoQuery;
use App\Modules\User\Query\UsersListQuery;
use App\Modules\User\Request\V1\ChangeForgottenPassword as ChangeForgottenPasswordRequestV1;
use App\Modules\User\Request\V1\ChangeUserActivation as ChangeUserActivationRequestV1;
use App\Modules\User\Request\V1\ChangeUserEmail as ChangeUserEmailRequestV1;
use App\Modules\User\Request\V1\ChangeUserPassword as ChangeUserPasswordRequestV1;
use App\Modules\User\Request\V1\ChangeUserRole as ChangeUserRoleRequestV1;
use App\Modules\User\Request\V1\RequestPasswordChange as RequestPasswordChangeRequestV1;
use App\Modules\User\Request\V1\ResendVerificationCode as ResendVerificationCodeRequestV1;
use App\Modules\User\Request\V1\UserRegister as UserRegisterRequestV1;
use App\Modules\User\Request\V1\VerifyEmail as VerifyEmailRequestV1;
use App\Shared\Command\Sync\CommandBus as SyncCommandBus;
use App\Shared\Exception\BaseException;
use App\Shared\Request\Validator\RequestValidator;
use App\Shared\Request\Validator\ValidationError;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Patch;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response as OAResponse;
use OpenApi\Attributes\Schema;
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
    public function register(UserRegisterRequestV1 $request): Response
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
    public function verifyEmail(VerifyEmailRequestV1 $request): Response
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
    public function resendVerificationCode(ResendVerificationCodeRequestV1 $request): Response
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
    public function requestPasswordChange(RequestPasswordChangeRequestV1 $request): Response
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
                required: ['email', 'password', 'repeatPassword', 'token'],
                properties: [
                    new Property(property: 'email', description: 'Email address', type: 'string', format: 'email'),
                    new Property(property: 'password', description: 'New password', type: 'string'),
                    new Property(property: 'repeatPassword', description: 'Repeat password', type: 'string'),
                    new Property(property: 'token', description: 'Verification token', type: 'string'),
                ],
                type: 'object'
            )
        ),
        tags: ['User', 'v1']
    )]
    #[IsGranted('UNAUTHENTICATED_USER')]
    #[Route('/api/v1/user/change_forgotten_password', name: 'v1.user.change_forgotten_password', methods: ['POST'])]
    public function changeForgottenPassword(ChangeForgottenPasswordRequestV1 $request): Response
    {
        $this->validator->validate($request);

        try {
            $this->syncCommandBus->dispatch($request->toCommand());
        } catch (UserNotFound|TokenDoesNotExists|TokenExpired|PasswordsDoNotMatch $exception) {
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
                        new Property(
                            property: 'isActivated',
                            description: 'Is user activated',
                            type: 'boolean',
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
    public function me(GetUserBasicInfoQuery $getUserBasicInfoQuery): Response
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
    public function changeUserRole(string $userId, ChangeUserRoleRequestV1 $request): Response
    {
        $request->id = $userId;

        $this->validator->validate($request);

        try {
            $this->syncCommandBus->dispatch($request->toCommand());
        } catch (BaseException $exception) {
            throw new ValidationError([
                ValidationError::VALIDATION => [$exception->getValidationKey()],
            ]);
        }

        return new JsonResponse([
            'status' => 'ok',
        ], Response::HTTP_OK);
    }

    #[Get(
        summary: 'Get list of users',
        tags: ['User', 'v1'],
        parameters: [
            new Parameter(
                name: 'page',
                description: 'Page number',
                in: 'query',
                required: false,
                schema: new Schema(type: 'integer'),
            ),
            new Parameter(
                name: 'limit',
                description: 'Limit number of results',
                in: 'query',
                required: false,
                schema: new Schema(type: 'integer'),
            ),
            new Parameter(
                name: 'searchPhrase',
                description: 'Search phrase for user (first name, last name and email)',
                in: 'query',
                required: false,
                schema: new Schema(type: 'string'),
            ),
        ],
        responses: [
            new OAResponse(
                response: 200,
                description: 'Returns paginated list of all class rooms',
                content: new JsonContent(
                    properties: [
                        new Property(property: 'page', type: 'integer', example: 2),
                        new Property(property: 'limit', type: 'integer', example: 7),
                        new Property(property: 'total', type: 'integer', example: 16),
                        new Property(property: 'totalPages', type: 'integer', example: 3),
                        new Property(
                            property: 'data',
                            type: 'array',
                            items: new Items(
                                properties: [
                                    new Property(property: 'id', type: 'string', format: 'uuid'),
                                    new Property(property: 'firstName', type: 'string'),
                                    new Property(property: 'lastName', type: 'string'),
                                    new Property(property: 'email', type: 'string'),
                                    new Property(property: 'createdAt', type: 'string'),
                                    new Property(property: 'isVerified', type: 'boolean'),
                                    new Property(property: 'isActivated', type: 'boolean'),
                                    new Property(property: 'role', type: 'string', example: 'ROLE_ADMIN'),
                                    new Property(property: 'teacherId', type: 'string', format: 'uuid', nullable: true),
                                    new Property(property: 'studentId', type: 'string', format: 'uuid', nullable: true),
                                ]
                            ),
                        ),
                    ]
                ),
            ),
        ],
    )]
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/v1/users/list', name: 'v1.users.list', methods: ['GET'])]
    public function usersList(UsersListQuery $query): Response
    {
        return new JsonResponse($query->execute());
    }

    #[Patch(
        summary: 'Change user activation',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                required: ['isActivated'],
                properties: [
                    new Property(property: 'isActivated', type: 'boolean'),
                ],
                type: 'object',
            ),
        ),
        tags: ['User', 'v1'],
        parameters: [
            new Parameter(
                name: 'userId',
                description: 'User ID',
                in: 'path',
                required: true,
            ),
        ],
    )]
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/v1/user/{userId}/change_activation', name: 'v1.user.change_activation', methods: ['PATCH'])]
    public function changeUserActivation(string $userId, ChangeUserActivationRequestV1 $request): Response
    {
        $request->userId = $userId;

        try {
            $this->syncCommandBus->dispatch($request->toCommand());
        } catch (UserNotFound|CannotChangeOwnActivation $exception) {
            throw new ValidationError([
                ValidationError::VALIDATION => [$exception->getValidationKey()],
            ]);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Patch(
        summary: 'Change email of currently logged user',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                required: ['email'],
                properties: [
                    new Property(property: 'email', type: 'string'),
                ],
                type: 'object',
            ),
        ),
        tags: ['User', 'v1'],
    )]
    #[Route('/api/v1/user/change_email', name: 'v1.user.change_email', methods: ['PATCH'])]
    public function changeUserEmail(ChangeUserEmailRequestV1 $request): Response
    {
        $this->validator->validate($request);

        try {
            $this->syncCommandBus->dispatch($request->toCommand());
        } catch (EmailAlreadyUsed $exception) {
            throw new ValidationError([
                ValidationError::VALIDATION => [$exception->getValidationKey()],
            ]);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Patch(
        summary: 'Change password of currently logged user',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                required: ['currentPassword', 'newPassword', 'newPasswordConfirmation'],
                properties: [
                    new Property(property: 'currentPassword', type: 'string'),
                    new Property(property: 'newPassword', type: 'string'),
                    new Property(property: 'newPasswordConfirmation', type: 'string'),
                ],
                type: 'object',
            ),
        ),
        tags: ['User', 'v1'],
    )]
    #[Route('/api/v1/user/change_password', name: 'v1.user.change_password', methods: ['PATCH'])]
    public function changeUserPassword(ChangeUserPasswordRequestV1 $request): Response
    {
        $this->validator->validate($request);

        try {
            $this->syncCommandBus->dispatch($request->toCommand());
        } catch (PasswordsDoNotMatch $exception) {
            throw new ValidationError([
                ValidationError::VALIDATION => [$exception->getValidationKey()],
            ]);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
