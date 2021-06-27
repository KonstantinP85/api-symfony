<?php

declare(strict_types=1);

namespace App\Security;

use App\DataProvider\UserDataProvider;
use App\Entity\User;
use App\Exception\AppException;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class UserAuthenticator extends AbstractGuardAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_security_login';

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var UserManager
     */
    private UserManager $userManager;

    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;

    /**
     * UserAuthenticator constructor.
     * @param UserManager $userManager
     * @param UserRepository $userRepository
     * @param UrlGeneratorInterface $urlGenerator
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        UserManager $userManager,
        UserRepository $userRepository,
        UrlGeneratorInterface $urlGenerator,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->userRepository= $userRepository;
        $this->userManager= $userManager;
        $this->urlGenerator = $urlGenerator;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return Response
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $data = ['errors' => 'Authentication required'];
        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request): bool
    {
        $credentials = $this->fetchCredentials($request);
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    /**
     * @param Request $request
     * @return array|mixed
     */
    public function getCredentials(Request $request)
    {
        $credentials = $this->fetchCredentials($request);
        $request->getSession()->set(Security::LAST_USERNAME, $credentials['username']);
        return $credentials;
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return User
     * @throws NonUniqueResultException
     */
    public function getUser($credentials, UserProviderInterface $userProvider): User
    {
        $user = $this->userRepository->loadUserByUserName($credentials['username']);
        if (!$user instanceof User) {
            throw new CustomUserMessageAuthenticationException('User could not be found');
        } elseif (!$user->isActive()) {
            throw new CustomUserMessageAuthenticationException('User was deactivated. Contact admin');
        }
        return $user;
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        $passwordCheck = $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
        if (!$passwordCheck) {
            throw new CustomUserMessageAuthenticationException('Incorrect password or login');
        }
        return $passwordCheck;
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse
     * @throws NonUniqueResultException
     * @throws AppException
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        $credentials = $this->fetchCredentials($request);
        $user = $this->userRepository->loadUserByUserName($credentials['username']);
        if ($user instanceof User) {
            $this->userManager->addUserLoginAttempt($user->getId());
        }
        $data = ['error' => strtr($exception->getMessageKey(), $exception->getMessageData())];
        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return Response|null
     * @throws AppException
     */
    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $providerKey
    ): ?Response {
        $targetPath = $this->getTargetPath($request->getSession(), $providerKey);
        if ($targetPath === null) {
            /**@var User $user */
            $user = $token->getUser();
            $userId = $user->getId();
            $this->userManager->clearUserLoginAttempts($userId);

            if ($user->isGranted(UserDataProvider::ROLE_ADMIN)) {
                $targetPath = $this->urlGenerator->generate('app_admin_default_index');
            } else {
                $targetPath = $this->urlGenerator->generate('app_front_default_index');
            }
        }
        return new RedirectResponse($targetPath);
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }

    /**
     * @param mixed $credentials
     * @return string|null
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    public function fetchCredentials($request): array
    {
        return json_decode($request->getContent(), true) ?? [];
    }
}