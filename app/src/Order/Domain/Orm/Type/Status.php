<?php

declare(strict_types=1);

namespace App\Order\Domain\Orm\Type;

enum Status: string
{
    case TAKEN = 'taken';
    case COLLECT = 'collect';
    case DIRECTED = 'directed';
    case RECEIVED = 'received';

    /** @return list<string> */
    public static function values(): array
    {
        return array_map(static fn (\UnitEnum $case) => $case->value, self::cases());
    }
}