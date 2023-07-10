<?php

declare(strict_types=1);

namespace App\Entity\Order\Resources\Fixtures;

use App\Doctrine\ORM\Fixture;
use App\Entity\Order\Domain\Orm\Type\Status;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Order\UseCase\Order\Add;

class OrderFixture extends Fixture
{
    public function __construct(private readonly Add\Handler $handler ) {

    }

    public function load(ObjectManager $manager)
    {
       for($i = 0; $i < 10; $i ++) {
           $command = new Add\Command([
               'status' => Status::TAKEN->value,
               'date_start_order' => date('d-m-y h:s:i'),
               'date_delivery' => date('d-m-y h:s:i'),
               'address' => [
                   'city' => 'Москва',
                   'street' => "Ленина",
                   'type_street' => 'street',
                   'home' => (string)$i,
                   'flat' => (string)$i
               ]
           ]);
           $this->handler->handle($command,true);
       }
    }
}