<?php

declare(strict_types=1);

namespace App\Framework\Exception;

class UnexpectedQueryResultException extends \DomainException
{
    public function __construct(string $type, mixed $value, int $code = 0)
    {
        $value = json_encode($value, \JSON_THROW_ON_ERROR);
        parent::__construct("expected {$type} value, {$value} given", $code);
    }
}