<?php

declare(strict_types=1);

namespace App\Doctrine\Dbal\Type;

class Types
{
    final public const UUID = 'uuid';
    final public const STRING = 'string';
    final public const SMALLINT = 'smallint';
    final public const DATE_MUTABLE = 'date';
    final public const DATE_IMMUTABLE = 'date_immutable';
    final public const DATETIME_MUTABLE = 'datetime';
    final public const DATETIME_IMMUTABLE = 'datetime_immutable';
    final public const DATETIMETZ_MUTABLE = 'datetimetz';
    final public const DATETIMETZ_IMMUTABLE = 'datetimetz_immutable';
}