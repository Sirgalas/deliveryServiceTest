<?php

declare(strict_types=1);

namespace App\Address\Domain\DTO;



use App\Framework\Dto\AbstractCommand;
use App\Address\Domain\Orm\Type\TypeStreet;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class AddressCommand extends AbstractCommand
{

    #[
        Groups(['default']),
        OA\Property(description: 'Город', type: 'string'),
        Assert\Length(max: 255),
        Assert\NotBlank
    ]
    public string $city;

    #[
        Groups(['default']),
        OA\Property(description: 'Другой населенный пункт', type: 'string', nullable: true),
        Assert\Length(max: 255),
    ]
    public ?string $village = null;

    #[
        Groups(['default']),
        OA\Property(description: 'Улица', type: 'string'),
        Assert\Length(max: 255),
        Assert\NotBlank
    ]
    public string $street;

    #[
        Groups(['default']),
        OA\Property(description: 'Тип улицы', type: 'string', default: 'street'),
        Assert\Choice(callback: [TypeStreet::class, 'values'])
    ]
    public string $type_street = 'street';

    #[
        Groups(['default']),
        OA\Property(description: 'Дом', type: 'string'),
        Assert\Length(max: 255),
        Assert\NotBlank
    ]
    public string $home;

    #[
        Groups(['default']),
        OA\Property(description: 'Корпус', type: 'string', nullable: true),
        Assert\Length(max: 255),
    ]
    public ?string $building = null;

    #[
        Groups(['default']),
        OA\Property(description: 'Квартира', type: 'integer'),
        Assert\Positive
    ]
    public string $flat;


    public function getTypeStreet(): TypeStreet
    {
        return TypeStreet::from($this->type_street);
    }
}