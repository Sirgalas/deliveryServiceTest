<?php

declare(strict_types=1);

namespace App\Exception;

class UnexpectedValueException extends \UnexpectedValueException
{
    public function __construct(string $type, mixed $value = null, int $code = 0)
    {
        $value = json_encode($value, \JSON_THROW_ON_ERROR);
        parent::__construct("expected {$type} value, {$value} given", $code);
    }
}