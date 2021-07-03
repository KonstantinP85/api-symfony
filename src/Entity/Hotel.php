<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\HotelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass=HotelRepository::class)
 * @ORM\Table(name="hotels")
 */
class Hotel
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(name="id", type="guid")
     */
    private string $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string")
     */
    private string $name;

    /**
     * @var string
     * @ORM\Column(name="description", type="text")
     */
    private string $description;

    /**
     * @var int
     * @ORM\Column(name="cost_one_day", type="integer")
     */
    private int $costOneDay;

    /**
     * @var string
     * @ORM\Column(name="address", type="string")
     */
    private string $address;

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
     * @var Collection
     * @ORM\OneToMany(targetEntity=Booking::class, mappedBy="hotel")
     */
    private Collection $bookings;

    /**
     * Hotel constructor.
     * @param string $name
     * @param string $description
     * @param int $costOneDay
     * @param string $address
     */
    public function __construct(string $name, string $description, int $costOneDay, string $address)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->name = $name;
        $this->description = $description;
        $this->costOneDay = $costOneDay;
        $this->address = $address;
        $this->bookings = new ArrayCollection();
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getCostOneDay(): int
    {
        return $this->costOneDay;
    }

    /**
     * @param int $costOneDay
     */
    public function setCostOneDay(int $costOneDay): void
    {
        $this->costOneDay = $costOneDay;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
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

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    /**
     * @param Booking $booking
     */
    public function addBooking(Booking $booking): void
    {
        if ($this->bookings->contains($booking)) {
            return;
        }

        $this->bookings->add($booking);
        $booking->setHotel($this);
    }

    /**
     * @param Booking $booking
     */
    public function removeBooking(Booking $booking): void
    {
        if (!$this->bookings->contains($booking)) {
            return;
        }

        $this->bookings->removeElement($booking);
    }
}