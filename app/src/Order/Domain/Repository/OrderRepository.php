<?php

declare(strict_types=1);

namespace App\Order\Domain\Repository;

use App\Framework\Doctrine\ORM\AbstractRepository;
use App\Order\Domain\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

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
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, Order::class);
    }
}