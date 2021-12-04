<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Entity\Hotel;

class HotelNormalizer extends AbstractCustomNormalizer
{
    public const CONTEXT_TYPE_KEY = 'hotel';
    public const TYPE_LIST = 'list';

    /**
     * @param Hotel $object
     * @param string|null $format
     * @param array $context
     * @return array
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        switch ($this->getType($context)) {
            case static::TYPE_LIST:
                $result = [
                    'id' => $object->getId(),
                    'name' => $object->getName(),
                    'address' => $object->getAddress()
                ];
                break;
            default:
                $result = [
                    'id' => $object->getId(),
                    'name' => $object->getName(),
                    'description' => $object->getDescription(),
                    'address' => $object->getAddress(),
                    'cost_one_day' => $object->getCostOneDay(),
                    'create_time' => $object->getCreateTime()->format('Y-m-d H:i:s'),
                    'update_time' => $object->getUpdateTime()->format('Y-m-d H:i:s')
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
        return $data instanceof Hotel;
    }
}