<?php

declare(strict_types=1);

namespace App\Framework\Doctrine\Dbal\Type;

use Webmozart\Assert\Assert;

class TableName extends AbstractStringType
{
    public function __construct(string $value)
    {
        $value = trim($value);
        Assert::maxLength($value, 63);

        $this->value = $value;
    }
}