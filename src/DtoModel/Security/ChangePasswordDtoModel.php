<?php

declare(strict_types=1);

namespace App\DtoModel\Security;

use App\DtoModel\BaseApiDtoModel;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordDtoModel extends BaseApiDtoModel
{
    /**
     * @var string
     * @SecurityAssert\UserPassword(
     *     message = "Wrong value for your current password"
     * )
     * @Assert\NotNull(message="Old password is required")
     */
    public string $oldPassword;

    /**
     * @var string
     * @Assert\NotNull(message="Old password is required")
     * @Assert\Length(
     *     min="5",
     *     minMessage="New password length is limited to {{ limit }} characters"
     * )
     */
    public string $newPassword;
}