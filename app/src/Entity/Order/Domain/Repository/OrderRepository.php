<?php

declare(strict_types=1);

namespace App\Entity\Order\Domain\Repository;

use App\Doctrine\ORM\AbstractRepository;
use App\Entity\Order\Domain\Entity\Order;

/**
 * @extends AbstractRepository<Order>
 *
 * @method Order      get($id);
 * @method Order|null find($id);
 * @method Order|null findOneBy(array $criteria, array $orderBy = null);
 * @method Order[]    findAll();
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);
 * @method Order[]    findByIds(array $ids);
 */
class OrderRepository extends AbstractRepository
{

}