<?php

declare(strict_types=1);

namespace App\Entity\Order\View;

use App\Doctrine\Dbal\Fetcher;
use App\Entity\Order\View\Commands\AddressCommand;
use App\Entity\Order\View\Commands\OneOrderCommand;

class OrderFetcher extends Fetcher
{
    public function one($order_id): OneOrderCommand
    {
        $qb = $this->getQueryBuilder()
            ->select([
                'id',
                'status',
                'date_start_order',
                'date_delivery',
                "(
                    SELECT json_agg(address) AS address
                    FROM (
                        SELECT json_build_object(
                            'city', a.city,
                            'village', a.village,
                            'street', a.street,
                            'value_type', a.value_type,
                            'home', a.home,
                            'building', a.building,
                            'flat', a.flat
                        ) AS address
                        FROM address a
                        WHERE a.id = o.address_id
                    ) AS address
                )",
            ])->from('orders o')
            ->where('o.id = :order_id')
            ->setParameter('order_id',$order_id);


            return new OneOrderCommand($this->executeQuery($qb)->fetchAssociative());
    }
}