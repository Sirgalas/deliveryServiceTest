<?php

declare(strict_types=1);

namespace App\Entity\Order\View\Commands;

use App\Dto\AbstractCommand;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

class OneOrderCommand extends AbstractCommand
{
    #[
        Groups(['default']),
        OA\Property(description: 'Идентификатор.', type: 'string')
    ]
    public $id;
    #[
        Groups(['default']),
        OA\Property(description: 'Статус.', type: 'string')
    ]
    public $status;
    #[
        Groups(['default']),
        OA\Property(description: 'Дата начала доставки.', type: 'string')
    ]
    public $date_start_order;
    #[
        Groups(['default']),
        OA\Property(description: 'Дата окончания доставки.', type: 'string')
    ]
    public $date_delivery;

    #[
        Groups(['default']),
        OA\Property(
            type: 'array',
            items: new OA\Items(
                ref: new Model(
                    type: AddressCommand::class,
                    groups: ['default']
                ),
                description: 'Элементы адресса.'
            )
        )
    ]
    public AddressCommand $address;

    public function setAddress(string | array $jsonOrArray): void
    {
        foreach ($this->getArray($jsonOrArray) as $value) {
            $this->address = new AddressCommand($value);
        }
    }
}