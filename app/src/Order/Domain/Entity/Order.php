<?php

declare(strict_types=1);

namespace App\Order\Domain\Entity;


use App\Framework\Doctrine\Dbal\Type\TableName;
use App\Framework\Doctrine\Dbal\Type\Types;
use App\Address\Domain\Entity\Address;
use App\Order\Domain\Orm\Type\Status;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[
    ORM\Entity,
    ORM\Table(
        name: self::TABLE_NAME,
        options:['comment' => 'Заказы']
    ),
]
class Order
{
    final public const TABLE_NAME = 'orders';


    #[
        ORM\Id,
        ORM\Column( name: 'id', type: Types::UUID, options: ['comment' => 'Идентификатор'])
    ]
    private Uuid $id;

    #[ORM\Column(
        name: 'status',
        enumType: Status::class,
        options: ['comment' => 'Статус.', 'default' => 'taken']
    )]
    private Status $status ;

    #[ORM\Column(
        name: 'date_start_order',
        type: Types::DATETIME_MUTABLE, options:['comment' => 'Дата поступления заказа', 'default' => 'CURRENT_TIMESTAMP']
    )]
    private \DateTime $dateStartOrder;

    #[ORM\Column(
        name: 'date_delivery',
        type: Types::DATETIME_MUTABLE, nullable: true, options:['comment' => 'Дата доставки']
    )]
    private ?\DateTime $dateDelivery;

    #[
        ORM\OneToOne(
            targetEntity: Address::class
        ),
       ORM\JoinColumn(
           name: "address_id",
           referencedColumnName: 'id',options: ['comment' => 'id таблицы Address']
       )
    ]
    private Address $address;

    public function __construct(
        Uuid $id,
        Status $status,
        \DateTime $dateStartOrder,
        Address $address,
        ?\DateTime $dateDelivery = null
    ) {
        $this->id = $id;
        $this->status = $status;
        $this->dateStartOrder = $dateStartOrder;
        $this->dateDelivery = $dateDelivery;
        $this->address = $address;
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

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): self
    {
        $this->address = $address;
        return $this;
    }

    public static function getTableName(): TableName
    {
        return new TableName(self::TABLE_NAME);
    }

    public function getDateStartOrder(): \DateTime
    {
        return $this->dateStartOrder;
    }

    public function setDateStartOrder(\DateTime $dateStartOrder): self
    {
        $this->dateStartOrder = $dateStartOrder;
        return $this;
    }

    public function getDateDelivery(): ?\DateTime
    {
        return $this->dateDelivery;
    }

    public function setDateDelivery(?\DateTime $dateDelivery): self
    {
        $this->dateDelivery = $dateDelivery;
        return $this;
    }
}