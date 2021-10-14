<?php

namespace App\Exception;

interface SeveralErrorsExceptionInterface
{
    /**
     * @return array
     */
    public function getErrors(): array;
}