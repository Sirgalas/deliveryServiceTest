<?php

declare(strict_types=1);

namespace App\Entity\Address\Domain\Orm\Type;

enum TypeStreet: string
{
    case BOULEVARD = 'boulevard';
    case AVENUE = 'avenue';
    case STREET = 'street';

    /** @return list<string> */
    public static function values(): array
    {
        return array_map(static fn (\UnitEnum $case) => $case->value, self::cases());
    }
}