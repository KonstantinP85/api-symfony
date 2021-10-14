<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Entity\User;

class UserNormalizer extends AbstractCustomNormalizer
{
    public const CONTEXT_TYPE_KEY = 'user';
    public const TYPE_LIST = 'list';
    public const TYPE_IN_BOOKING = 'in_booking';

    /**
     * @param User $object
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
                    'fullName' => $object->getLastName() . $object->getFirstName(),
                    'phone' => $object->getPhone(),
                    'email' => $object->getEmail()
                ];
                break;
            case self::TYPE_IN_BOOKING:
                $result = [
                    'id' => $object->getId(),
                    'fullName' => $object->getLastName() . $object->getFirstName(),
                ];
                break;
            default:
                $result = [
                    'id' => $object->getId(),
                    'firstName' => $object->getFirstName(),
                    'lastName' => $object->getLastName(),
                    'patronymic' => $object->getPatronymic(),
                    'email' => $object->getEmail(),
                    'phone' => $object->getPhone(),
                    'active' => $object->isActive(),
                    'roles' => $object->getRoles(),
                    'createTime' => $object->getCreateTime()->format('Y-m-d H:i:s'),
                    'updateTime' => $object->getUpdateTime()->format('Y-m-d H:i:s'),
                    'bookings' => $object->getBookings()
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
        return $data instanceof User;
    }
}