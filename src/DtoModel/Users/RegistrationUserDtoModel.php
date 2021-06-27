<?php

declare(strict_types=1);

namespace App\DtoModel\Users;

use App\DtoModel\BaseApiDtoModel;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationUserDtoModel extends BaseApiDtoModel
{
    /**
     * @var string
     * @Assert\Email(message="Not corect email")
     * @Assert\NotBlank(message="Email is required")
     */
    public string $email;

    /**
     * @var string
     * @Assert\NotBlank(message="First name is required")
     */
    public string $firstName;

    /**
     * @var string
     * @Assert\NotBlank(message="Last name is required")
     */
    public string $lastName;

    /**
     * @var string
     * @Assert\NotBlank(message="Phone is required")
     */
    public string $phone;

    /**
     * @var string
     * @Assert\NotBlank(message="Password is required")
     * @Assert\Length(
     *     min="5",
     *     minMessage="Password length is limited to {{ limit }} characters"
     * )
     */
    public string $password;

    /**
     * @var string
     * @Assert\NotBlank(message="Confirm password is required")
     * @Assert\Length(
     *     min="5",
     *     minMessage="Confirm password length is limited to {{ limit }} characters"
     * )
     */
    public string $confirmPassword;

    /**
     * @var string|null
     */
    public ?string $patronymic;
}