<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\EmailConfirmationToken;
use App\Entity\User;
use App\Exception\AppException;
use App\Repository\EmailConfirmationTokenRepository;
use App\Traits\EntityManagerTrait;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Url;
use Symfony\Component\HttpFoundation\Response;

class EmailConfirmationTokenManager
{
    use EntityManagerTrait;

    /**
     * @var EmailConfirmationTokenRepository
     */
    private EmailConfirmationTokenRepository $repository;

    /**
     * EmailConfirmationTokenManager constructor.
     * @param EmailConfirmationTokenRepository $repository
     */
    public  function __construct(EmailConfirmationTokenRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param User $user
     * @return EmailConfirmationToken
     */
    public function createEmailConfirmation(User $user): EmailConfirmationToken
    {
        $emailConfirmationToken = new EmailConfirmationToken($user);
        $this->entityManager->persist($emailConfirmationToken);
        return $emailConfirmationToken;
    }

    /**
     * @param string $token
     * @return EmailConfirmationToken
     * @throws AppException
     */
    public function getEmailConfirmationToken(string $token): EmailConfirmationToken
    {
        $emailConfirmationToken = $this->repository->findOneBy(['token' => $token]);
        if ($emailConfirmationToken instanceof EmailConfirmationToken) {
            return $emailConfirmationToken;
        }
        throw new AppException('Confirmation token not found', Response::HTTP_NOT_FOUND);
    }
}