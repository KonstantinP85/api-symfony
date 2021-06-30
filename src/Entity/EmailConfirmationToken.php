<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EmailConfirmationTokenRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass=EmailConfirmationTokenRepository::class)
 * @ORM\Table(name="email_confirmation_tokens")
 */
class EmailConfirmationToken
{
    const EMAIL_CONFIRMATION_TOKEN_LIFETIME = 86400;

    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(name="id", type="guid")
     */
    private string $id;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private User $user;

    /**
     * @var string
     * @ORM\Column(name="token", type="string")
     */
    private string $token;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(name="create_time", type="datetime_immutable")
     */
    private \DateTimeImmutable $createTime;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(name="expire_time", type="datetime_immutable")
     */
    private \DateTimeImmutable $expireTime;

    /**
     * EmailConfirmationToken constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->user = $user;
        $this->token = md5(uniqid('', true));
        $now = new \DateTimeImmutable();
        $this->createTime = $now;
        $this->expireTime = $now->modify("+ " . self::EMAIL_CONFIRMATION_TOKEN_LIFETIME . " seconds");
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreateTime(): \DateTimeImmutable
    {
        return $this->createTime;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getExpireTime(): \DateTimeImmutable
    {
        return $this->expireTime;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        $now = new \DateTimeImmutable();
        return ($now < $this->expireTime);
    }
}