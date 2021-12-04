<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Entity\Booking;

class BookingNormalizer extends AbstractCustomNormalizer
{
    public const CONTEXT_TYPE_KEY = 'booking';
    public const TYPE_LIST = 'list';

    /**
     * @param Booking $object
     * @param string|null $format
     * @param array $context
     * @return array
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        switch ($this->getType($context)) {
            case self::TYPE_LIST:
                $result = [
                    'id' => $object->getId(),
                    'hotel' => $object->getHotel()->getName(),
                    'user' => $this->normalizer->normalize(
                        $object->getUser(),
                        $format,
                        [UserNormalizer::CONTEXT_TYPE_KEY => UserNormalizer::TYPE_IN_BOOKING]
                    ),
                    'arrivalTime' => $object->getArrivalTime()->format('Y-m-d H:i:s'),
                    'duration' => $object->getDuration(),
                    'status' => $object->getStatus(),
                ];
                break;
            default:
                $result = [
                    'id' => $object->getId(),
                    'hotel' => $object->getHotel()->getName(),
                    'user' => $this->normalizer->normalize(
                        $object->getUser(),
                        $format,
                        [UserNormalizer::CONTEXT_TYPE_KEY => UserNormalizer::TYPE_IN_BOOKING]
                    ),
                    'arrivalTime' => $object->getArrivalTime()->format('Y-m-d H:i:s'),
                    'duration' => $object->getDuration(),
                    'status' => $object->getStatus(),
                    'createTime' => $object->getCreateTime()->format('Y-m-d H:i:s'),
                    'updateTime' => $object->getUpdateTime()->format('Y-m-d H:i:s'),
                    'bookingHistory' => $this->normalizer->normalize(
                        $object->getUser(),
                        $format,
                        [BookingHistoryNormalizer::CONTEXT_TYPE_KEY => BookingHistoryNormalizer::TYPE_IN_BOOKING]
                    ),
                ];
        }

        return $result;
    }

    /**
     * @param mixed $data
     * @param string|null $format
     * @return bool
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Booking;
    }
}