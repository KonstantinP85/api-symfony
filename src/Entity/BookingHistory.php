<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\BookingHistoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass=BookingHistoryRepository::class)
 * @ORM\Table(name="booking_history")
 */
class BookingHistory
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(name="id", type="guid")
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity=Booking::class, inversedBy="history")
     * @ORM\JoinColumn(name="booking_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private Booking $booking;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(name="create_time", type="datetime_immutable")
     */
    private \DateTimeImmutable $createTime;

    /**
     * @var string
     * @ORM\Column(name="who", type="string")
     */
    private string $who;

    /**
     * @var string
     * @ORM\Column(name="new_value", type="string")
     */
    private string $newValue;

    /**
     * @param Booking $booking
     * @param string $who
     * @param string $newValue
     */
    public function __construct(Booking $booking, string $who,  string $newValue)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->booking = $booking;
        $booking->addBookingHistory($this);
        $this->who = $who;
        $this->newValue = $newValue;
        $this->createTime = new \DateTimeImmutable();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return Booking
     */
    public function getBooking(): Booking
    {
        return $this->booking;
    }

    /**
     * @param Booking $booking
     */
    public function setBooking(Booking $booking): void
    {
        if ($this->booking === $booking) {
            return;
        }

        $this->booking = $booking;
        $booking->addBookingHistory($this);
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreateTime(): \DateTimeImmutable
    {
        return $this->createTime;
    }

    /**
     * @return string
     */
    public function getWho(): string
    {
        return $this->who;
    }

    /**
     * @param string $who
     */
    public function setWho(string $who): void
    {
        $this->who = $who;
    }

    /**
     * @return string
     */
    public function getNewValue(): string
    {
        return $this->newValue;
    }

    /**
     * @param string $newValue
     */
    public function setNewValue(string $newValue): void
    {
        $this->newValue = $newValue;
    }
}