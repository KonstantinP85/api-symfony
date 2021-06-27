<?php

declare(strict_types=1);

namespace App\Traits;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;

trait UserTokenStorageTrait
{
    /**
     * @var Security
     */
    protected Security $security;

    /**
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @return User|null
     */
    public function getLoggedInUser(): ?User
    {
        $user = $this->security->getUser();
        if ($user instanceof User) {
            return $user;
        }
        return null;
    }
}