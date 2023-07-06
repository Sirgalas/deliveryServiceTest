<?php

declare(strict_types=1);

namespace App\Entity\Order\Domain\Orm\Type;

enum Status: string
{
    case TAKEN = 'taken';
    case COLLECT = 'collect';
    case DIRECTED = 'directed';
    case RECEIVED = 'received';

}