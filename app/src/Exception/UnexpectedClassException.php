<?php

declare(strict_types=1);

namespace App\Exception;

class UnexpectedClassException extends \DomainException
{
    public function __construct(string $expectedClassName, string $unexpectedClassName)
    {
        parent::__construct("expected class {$expectedClassName}, {$unexpectedClassName} given");
    }
}