<?php

declare(strict_types=1);

namespace App\Order\UseCase\Order\Add;

use App\Framework\Doctrine\ORM\Flusher;
use App\Address\Domain\Entity\Address;
use App\Address\Domain\Repository\AddressRepository;
use App\Order\Domain\Entity\Order;
use App\Order\Domain\Repository\OrderRepository;
use Symfony\Component\Uid\Uuid;

class Handler
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly Flusher $flusher,
        private readonly AddressRepository $addressRepository
    )
    {
    }

    public function handle(Command $command, bool $flush = false): Order
    {
        $address = new Address(
            id: Uuid::v4(),
            city: $command->address->city,
            village: $command->address->village,
            street: $command->address->street,
            typeStreet: $command->address->getTypeStreet(),
            home: $command->address->home,
            building: $command->address->building,
            flat: $command->address->flat
        );
        $this->addressRepository->add($address);

        $order = new Order(
            id: Uuid::v4(),
            status: $command->getStatus(),
            dateStartOrder:$command->getDateStartOrder(),
            address: $address,
            dateDelivery:$command->getDateDelivery()
        );
        $this->orderRepository->add($order);

        if($flush){
            $this->flusher->flush();
        }
        return $order;
    }
}