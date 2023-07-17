<?php

declare(strict_types=1);

namespace App\Order\Resources\Tests;

use App\Framework\Exception\UnexpectedQueryResultException;
use App\Framework\Test\TestKernel;

class OrderTestKernel extends TestKernel
{
    public function getOrderId(): string
    {
        $orderId = $this->getQueryBuilder()
            ->select(['o.id'])
            ->from('order o')
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchOne();
        if(!\is_string($orderId)) {
            throw new UnexpectedQueryResultException('uuid', $orderId);
        }
        return $orderId;
    }
}