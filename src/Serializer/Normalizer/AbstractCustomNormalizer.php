<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use Symfony\Component\Serializer\Encoder\NormalizationAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

abstract class AbstractCustomNormalizer implements NormalizationAwareInterface, NormalizerInterface
{
    use NormalizerAwareTrait;

    public const CONTEXT_TYPE_KEY = null;
    public const DEFAULT_TYPE_KEY = null;

    public function getType(array $context): ?string
    {
        if (!self::CONTEXT_TYPE_KEY) {
            return null;
        }

        return $context[self::CONTEXT_TYPE_KEY] ?? self::DEFAULT_TYPE_KEY;
    }
}