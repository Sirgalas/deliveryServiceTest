<?php

declare(strict_types=1);

namespace App\Entity\Address\Domain\Orm\Type;

enum TypeStreet: string
{
    case BOULEVARD = 'boulevard';
    case AVENUE = 'avenue';
    case STREET = 'street';
}