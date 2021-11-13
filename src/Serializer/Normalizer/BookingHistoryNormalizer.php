<?php

namespace App\Serializer\Normalizer;

use App\Entity\BookingHistory;

class BookingHistoryNormalizer extends AbstractCustomNormalizer
{
    public const CONTEXT_TYPE_KEY = 'booking_history';
    public const TYPE_LIST = 'list';
    public const TYPE_IN_BOOKING = 'in_booking';

    /**
     * @param BookingHistory $object
     * @param string|null $format
     * @param array $context
     * @return array
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        switch ($this->getType($context)) {
            case self::TYPE_IN_BOOKING:
                $result = [
                    'id' => $object->getId(),
                    'createTime' => $object->getCreateTime()->format('Y-m-d H:i:s'),
                    'who' => $object->getWho(),
                    'newValue' => $object->getNewValue()
                ];
                break;
            default:
                $result = [
                    'id' => $object->getId(),
                    'createTime' => $object->getCreateTime()->format('Y-m-d H:i:s'),
                    'who' => $object->getWho(),
                    'newValue' => $object->getNewValue(),
                    'booking' => $object->getBooking()->getId()
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
        return $data instanceof BookingHistory;
    }
}