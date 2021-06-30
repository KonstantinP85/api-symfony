<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintsValidationsException extends DataValidationException
{
    /**
     * ConstraintsValidationsException constructor.
     * @param ConstraintViolationListInterface $errors
     * @param int $code
     * @param string|null $message
     * @throws \Exception
     */
    public function __construct(
        ConstraintViolationListInterface $errors,
        int $code = Response::HTTP_BAD_REQUEST,
        ?string $message = null
    ) {
        parent::__construct($this->constraintsToArray($errors), $code, $message);
    }

    /**
     * @param ConstraintViolationListInterface $object
     * @return array
     * @throws \Exception
     */
    public function constraintsToArray(ConstraintViolationListInterface $object): array
    {
        $result = [];
        if ($object instanceof ConstraintViolationList) {
            foreach ($object->getIterator() as $error) {
                $result[$error->getPropertyPath()][] = [
                    'error' => $error->getMessage(),
                ];
            }
        }

        return $result;
    }
}