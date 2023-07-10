<?php

declare(strict_types=1);

namespace App\Entity\Address\Domain\Repository;


use App\Doctrine\ORM\AbstractRepository;
use App\Entity\Address\Domain\Entity\Address;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends AbstractRepository<Address>
 * @method Address      get($id);
 * @method Address|null find($id);
 * @method Address|null findOneBy(array $criteria, array $orderBy = null);
 * @method Address[]    findAll();
 * @method Address[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);
 * @method Address[]    findByIds(array $ids);
 */
class AddressRepository extends AbstractRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, Address::class);
    }
}