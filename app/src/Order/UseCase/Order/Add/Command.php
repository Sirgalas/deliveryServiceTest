<?php

declare(strict_types=1);

namespace App\Order\UseCase\Order\Add;

use App\Framework\Dto\AbstractCommand;
use App\Address\Domain\DTO\AddressCommand;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Order\Domain\Orm\Type\Status;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
class Command extends AbstractCommand
{
    #[
        Groups(['default']),
        OA\Property(description: 'Статус', type: 'string', default: 'taken'),
        Assert\Choice(callback: [Status::class, 'values'])
    ]
    public string $status = 'taken';

    #[
        Groups(['default']),
        OA\Property(description: 'Улица', type: 'string', nullable: true),
        Assert\Length(max: 255),
        Assert\NotBlank
    ]
    public string $date_start_order ;

    #[
        Groups(['default']),
        OA\Property(description: 'Улица', type: 'string', nullable: true),
        Assert\Length(max: 255),
        Assert\NotBlank
    ]
    public string $date_delivery;

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
    public ?AddressCommand $address;

    public function getStatus(): Status
    {
        return Status::from($this->status);
    }

    public function getDateStartOrder(): \DateTime
    {
        return new \DateTime($this->date_start_order);
    }
    public function getDateDelivery(): \DateTime
    {
        return new \DateTime($this->date_delivery);
    }

    public function setAddress(array $address): void
    {
        $this->address = new AddressCommand($address);
    }
}