<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\User;
use App\Exception\AppException;
use App\Traits\EntityManagerTrait;
use App\Traits\UserTokenStorageTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityManager
{
    use EntityManagerTrait;
    use UserTokenStorageTrait;

    /**
     * @var EmailConfirmationTokenManager
     */
    private EmailConfirmationTokenManager $emailConfirmationTokenManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;

    /**
     * SecurityManager constructor.
     * @param EmailConfirmationTokenManager $emailConfirmationTokenManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public  function __construct(
        EmailConfirmationTokenManager $emailConfirmationTokenManager,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->emailConfirmationTokenManager = $emailConfirmationTokenManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @throws AppException
     */
    public function confirmEmail(string $token): void
    {
        $confirmationToken = $this->emailConfirmationTokenManager->getEmailConfirmationToken($token);
        if (!$confirmationToken->isValid()) {
            $this->entityManager->remove($confirmationToken);
            $this->entityManager->flush();
            throw new AppException('Confirmation token not found', Response::HTTP_NOT_FOUND);
        }
        $user = $confirmationToken->getUser();
        if ($user !== null) {
            $user->setActive(true);
        }
        $this->entityManager->remove($confirmationToken);
        $this->entityManager->flush();
    }

    /**
     * @param string $oldPassword
     * @param string $newPassword
     * @throws AppException
     */
    public function changePassword(string $oldPassword, string $newPassword): void
    {
        $user = $this->getLoggedInUser();
        if (!$user instanceof User) {
            throw new AppException('Auth error', Response::HTTP_UNAUTHORIZED);
        }
        if ($this->passwordEncoder->isPasswordValid($user, $oldPassword)) {
            $encoded = $this->passwordEncoder->encodePassword($user, $newPassword);
            $user->setPassword($encoded);
            $this->entityManager->flush();
        } else {
            throw new AppException('Old password is incorrect', Response::HTTP_BAD_REQUEST);
        }
    }
}