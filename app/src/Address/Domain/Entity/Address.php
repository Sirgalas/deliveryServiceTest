<?php

declare(strict_types=1);

namespace App\Address\Domain\Entity;


use App\Framework\Doctrine\Dbal\Type\Types;
use App\Framework\Doctrine\ORM\Contracts\Identity;
use App\Address\Domain\Orm\Type\TypeStreet;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

//use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\DependencyInjection\Loader\FileLoader;

#[
    ORM\Entity,
    ORM\Table(
        name: self::TABLE_NAME,
        options:['comment' => 'Адресс']
    ),
]
class Address implements Identity
{
    final public const TABLE_NAME = 'address';

    #[
        ORM\Id,
        ORM\Column( name: 'id', type: Types::UUID, options: ['comment' => 'Идентификатор'])
    ]
    private Uuid $id;

    #[ORM\Column(
        name: 'city',
        type: Types::STRING,
        nullable: true,
        options: ['comment' => 'Город.'])]
    private string $city;

    #[ORM\Column(
        name: 'village',
        type: Types::STRING,
        nullable: true,
        options: ['comment' => 'Другой населеный пункт.'])
    ]
    private ?string $village = null;

    #[ORM\Column(
        name: 'street',
        type: Types::STRING,
        nullable: true,
        options: ['comment' => 'Улица.'])
    ]
    private string $street;

    #[ORM\Column(
        name: 'value_type',
        enumType: TypeStreet::class,
        options: ['comment' => 'Тип улицы.', 'default' => 'street']
    )]
    private TypeStreet $typeStreet;

    #[ORM\Column(
        name: 'home',
        type: Types::STRING,
        nullable: true,
        options: ['comment' => 'Дом.'])
    ]
    private string $home;

    #[ORM\Column(
        name: 'building',
        type: Types::STRING,
        nullable: true,
        options: ['comment' => 'Корпус.'])
    ]
    private ?string $building = null;

    #[ORM\Column(
        name: 'flat',
        type: Types::SMALLINT,
        nullable: true,
        options: ['comment' => 'Квартира.'])
    ]
    private string $flat;

    public function __construct(
        Uuid $id,
        string $city,
        ?string $village,
        string $street,
        TypeStreet $typeStreet,
        string $home,
        ?string $building,
        string $flat
    ) {
        $this->id = $id;
        $this->city = $city;
        $this->village = $village;
        $this->street = $street;
        $this->typeStreet = $typeStreet;
        $this->home = $home;
        $this->building = $building;
        $this->flat = $flat;
    }


    public function getId(): Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getVillage(): ?string
    {
        return $this->village;
    }

    public function setVillage(?string $village): self
    {
        $this->village = $village;
        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;
        return $this;
    }

    public function getStatus(): TypeStreet
    {
        return $this->status;
    }

    public function setStatus(TypeStreet $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getHome(): ?string
    {
        return $this->home;
    }

    public function setHome(?string $home): self
    {
        $this->home = $home;
        return $this;
    }

    public function getBuilding(): ?string
    {
        return $this->building;
    }

    public function setBuilding(?string $building): self
    {
        $this->building = $building;
        return $this;
    }

    public function getFlat(): ?string
    {
        return $this->flat;
    }

    public function setFlat(?string $flat): self
    {
        $this->flat = $flat;
        return $this;
    }

    public function getTypeStreet(): TypeStreet
    {
        return $this->typeStreet;
    }

    public function setTypeStreet(TypeStreet $typeStreet): void
    {
        $this->typeStreet = $typeStreet;
    }
}