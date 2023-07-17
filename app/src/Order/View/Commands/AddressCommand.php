<?php

declare(strict_types=1);

namespace App\Order\View\Commands;

use App\Framework\Dto\AbstractCommand;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

class AddressCommand extends AbstractCommand
{
    #[
        Groups(['default']),
        OA\Property(description: 'Город', type: 'string', format: 'Москва')
    ]
    public $city;
    #[
        Groups(['default']),
        OA\Property(description: 'Другой населеный пункт', type: 'string', format: 'деревня Гадюкино')
    ]
    public $village;
    #[
        Groups(['default']),
        OA\Property(description: 'улица', type: 'string', format: 'Ленина')
    ]
    public $street;
    #[
        Groups(['default']),
        OA\Property(description: 'Тип улицы', type: 'string', format: 'street')
    ]
    public $type_street;
    #[
        Groups(['default']),
        OA\Property(description: 'номер дома', type: 'string', format: '1')
    ]
    public $home;

    #[
        Groups(['default']),
        OA\Property(description: 'корпус', type: 'string', format: '1')
    ]
    public $building;

    #[
        Groups(['default']),
        OA\Property(description: 'квартира', type: 'string', format: '1')
    ]
    public $flat;
}