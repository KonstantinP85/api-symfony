<?php

namespace App\EventListener;

use App\DataProvider\UserDataProvider;
use App\Entity\Booking;
use App\Entity\BookingHistory;
use App\Entity\User;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BookingHistoryEventListener
{
    /**
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $tokenStorage;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param LoggerInterface $logger
     */
    public function __construct(TokenStorageInterface $tokenStorage, LoggerInterface $logger)
    {
        $this->tokenStorage = $tokenStorage;
        $this->logger = $logger;
    }

    /**
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(onFlushEventArgs $eventArgs): void
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof Booking) {
                $user = $this->tokenStorage->getToken()->getUser();
                if ($user instanceof User) {
                    $who = in_array(UserDataProvider::ROLE_ADMIN, $user->getRoles())
                        ? UserDataProvider::USER_TYPE_ADMIN
                        : UserDataProvider::USER_TYPE_CLIENT;
                } else {
                    $this->logger->info('Unknown user', ['bookingId' => $entity->getId()]);
                    $who = UserDataProvider::USER_TYPE_UNKNOWN;
                }

                $bookingHistory = new BookingHistory($entity, $who, $entity->getStatus());
                $em->persist($bookingHistory);
                $uow->computeChangeSet($em->getClassMetadata(BookingHistory::class), $bookingHistory);
                $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(Booking::class), $entity);
            }
        }
        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof Booking) {
                $user = $this->tokenStorage->getToken()->getUser();
                if ($user instanceof User) {
                    $who = in_array(UserDataProvider::ROLE_ADMIN, $user->getRoles())
                        ? UserDataProvider::USER_TYPE_ADMIN
                        : UserDataProvider::USER_TYPE_CLIENT;
                } else {
                    $this->logger->info('Unknown user', ['bookingIid' => $entity->getId()]);
                    $who = UserDataProvider::USER_TYPE_UNKNOWN;
                }

                $changeArray = $uow->getEntityChangeSet($entity);

                if (array_key_exists('status', $changeArray) && $changeArray['status'][0] !== $changeArray['status'][1]) {
                    $bookingHistory = new BookingHistory($entity, $who, $changeArray['status'][1]);
                    $em->persist($bookingHistory);
                    $uow->computeChangeSet($em->getClassMetadata(BookingHistory::class), $bookingHistory);
                }
                $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(Booking::class), $entity);
            }
        }
    }
}