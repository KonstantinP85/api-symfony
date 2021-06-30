<?php

declare(strict_types=1);

namespace App\Manager;

use App\DataProvider\UserDataProvider;
use App\Entity\User;
use App\Exception\AppException;
use App\Repository\UserRepository;
use App\Traits\EntityManagerTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManager
{
    use EntityManagerTrait;

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;

    /**
     * @var MailManager
     */
    private MailManager $mailManager;

    /**
     * @var EmailConfirmationTokenManager
     */
    private EmailConfirmationTokenManager $emailConfirmationTokenManager;

    /**
     * UserAuthenticator constructor.
     * @param UserRepository $userRepository
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param MailManager $mailManager
     * @param EmailConfirmationTokenManager $emailConfirmationTokenManager
     */
    public function __construct(
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        MailManager $mailManager,
        EmailConfirmationTokenManager $emailConfirmationTokenManager
    ) {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->mailManager = $mailManager;
        $this->emailConfirmationTokenManager = $emailConfirmationTokenManager;
    }

    /**
     * @param string $id
     * @return User
     * @throws AppException
     */
    public function addUserLoginAttempt(string $id): User
    {
        $user = $this->get($id);
        $user->addLoginAttempt();
        if ($user->isLoginAttemptsOverLimit()) {
            $user->setActive(false);
        }
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @param string $id
     * @return User
     * @throws AppException
     */
    public function clearUserLoginAttempts(string $id): User
    {
        $user = $this->get($id);
        $user->clearLoginAttempt();
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @param string $id
     * @return User
     * @throws AppException
     */
    public function get(string $id): User
    {
        $user = $this->userRepository->find($id);
        if (!$user instanceof User) {
            throw new AppException('Auth error', Response::HTTP_UNAUTHORIZED);
        }

        return $user;
    }

    /**
     * @param string $firstName
     * @param string $lastName
     * @param array $roles
     * @param string $email
     * @param string $phone
     * @param string $password
     * @param string|null $patronymic
     * @return User
     */
    public function create(
        string $firstName,
        string $lastName,
        array $roles,
        string $email,
        string $phone,
        string $password,
        ?string $patronymic
    ): User {
        $user = new User($firstName, $lastName, $roles, $email, $phone, $patronymic);
        $encoded = $this->passwordEncoder->encodePassword($user, $password);
        $user->setPassword($encoded);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $phone
     * @param string $password
     * @param string $confirmPassword
     * @param string|null $patronymic
     * @return User
     * @throws AppException
     */
    public function registration(
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $password,
        string $confirmPassword,
        ?string $patronymic
    ): User {
        if ($password !== $confirmPassword) {
            throw new AppException('Passwords not equal', Response::HTTP_BAD_REQUEST);
        }
        $user = $this->userRepository->findOneBy(['phone' => $phone]);
        if ($user instanceof User) {
            throw new AppException('Not correct phone', Response::HTTP_BAD_REQUEST);
        }
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if ($user instanceof User) {
            throw new AppException('Not correct email', Response::HTTP_BAD_REQUEST);
        }
        $user = $this->create(
            $firstName,
            $lastName,
            [UserDataProvider::ROLE_USER],
            $email,
            $phone,
            $password,
            $patronymic
        );
        $emailConfirmationToken = $this->emailConfirmationTokenManager->createEmailConfirmation($user);
        $this->entityManager->flush();
        $this->mailManager->sendEmailConfirmationTokenEmail($user, $emailConfirmationToken->getToken());

        return $user;
    }
}