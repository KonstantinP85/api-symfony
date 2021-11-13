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
    public Security $security;

    /**
     * @required
     * @param Security $security
     */
    public function setSecurity(Security $security): void
    {
        $this->security = $security;
    }

    /**
     * @required
     * @return User|null
     */
    public function getLoggedInUser(): ?User
    {
        $user = $this->security->getToken()->getUser();
        if ($user instanceof User) {
            return $user;
        }

        return null;
    }
}