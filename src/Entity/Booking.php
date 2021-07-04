<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\BookingRepository;
use App\DataProvider\BookingDataProvider;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 * @ORM\Table(name="bookings")
 */
class Booking
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(name="id", type="guid")
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity=Hotel::class, inversedBy="bookings")
     * @ORM\JoinColumn(name="hotel_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private Hotel $hotel;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="bookings")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private User $user;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(name="arrival_time", type="datetime_immutable")
     */
    private \DateTimeImmutable $arrivalTime;

    /**
     * @var int
     * @ORM\Column(name="duration", type="integer")
     */
    private int $duration;

    /**
     * @var string
     * @ORM\Column(name="status", type="string")
     */
    private string $status;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(name="create_time", type="datetime_immutable")
     */
    private \DateTimeImmutable $createTime;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(name="update_time", type="datetime_immutable")
     */
    private \DateTimeImmutable $updateTime;

    /**
     * Booking constructor.
     * @param Hotel $hotel
     * @param User $user
     * @param \DateTimeImmutable $arrivalTime
     * @param int $duration
     */
    public function __construct(Hotel $hotel, User $user, \DateTimeImmutable $arrivalTime, int $duration)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->hotel = $hotel;
        $hotel->addBooking($this);
        $this->user = $user;
        $user->addBooking($this);
        $this->arrivalTime = $arrivalTime;
        $this->duration = $duration;
        $this->status = BookingDataProvider::BOOKING_STATUS_NEW;
        $date = new \DateTimeImmutable();
        $this->createTime = $date;
        $this->updateTime = $date;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return Hotel
     */
    public function getHotel(): Hotel
    {
        return $this->hotel;
    }

    /**
     * @param Hotel $hotel
     */
    public function setHotel(Hotel $hotel): void
    {
        if ($this->hotel === $hotel) {
            return;
        }

        $this->hotel = $hotel;
        $hotel->addBooking($this);
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        if ($this->user === $user) {
            return;
        }

        $this->user = $user;
        $user->addBooking($this);
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getArrivalTime(): \DateTimeImmutable
    {
        return $this->arrivalTime;
    }

    /**
     * @param \DateTimeImmutable $arrivalTime
     */
    public function setArrivalTime(\DateTimeImmutable $arrivalTime): void
    {
        $this->arrivalTime = $arrivalTime;
    }

    /**
     * @return int
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate(): void
    {
        $this->updateTime = new \DateTimeImmutable();
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
    public function getUpdateTime(): \DateTimeImmutable
    {
        return $this->updateTime;
    }
}