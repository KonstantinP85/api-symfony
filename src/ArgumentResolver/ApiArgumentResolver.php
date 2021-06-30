<?php

declare(strict_types=1);

namespace App\ArgumentResolver;

use App\DtoModel\BaseApiDtoModelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class ApiArgumentResolver implements ArgumentValueResolverInterface
{
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * ApiArgumentResolver constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return bool
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        try {
            $reflection = new \ReflectionClass($argument->getType());
            if ($reflection->implementsInterface(BaseApiDtoModelInterface::class)) {
                return true;
            }
            return false;
        } catch (\ReflectionException $exception) {
            return false;
        }
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return \Generator
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $content = $request->getContent() ?: '{}';
        yield $this->serializer->deserialize($content, $argument->getType(), JsonEncoder::FORMAT);
    }
}