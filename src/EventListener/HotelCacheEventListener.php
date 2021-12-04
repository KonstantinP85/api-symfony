<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Hotel;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class HotelCacheEventListener
{
    /**
     * @var CacheInterface
     */
    private CacheInterface $hotelCache;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param CacheInterface $hotelCache
     * @param LoggerInterface $logger
     */
    public function __construct(CacheInterface $hotelCache, LoggerInterface $logger)
    {
        $this->hotelCache = $hotelCache;
        $this->logger = $logger;
    }

    /**
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function postUpdate(LifecycleEventArgs $event)
    {
        $entity = $event->getObject();

        if ($entity instanceof Hotel) {
            try {
                $this->hotelCache->delete($entity->getId());
            } catch (InvalidArgumentException $e) {
                $this->logger->info('Error clearing the cache: ' . $e->getMessage(), ['hotel_id' => $entity->getId()]);
            }
        }
    }
}