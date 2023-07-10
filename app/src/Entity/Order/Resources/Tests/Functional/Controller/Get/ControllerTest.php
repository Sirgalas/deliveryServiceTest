<?php

declare(strict_types=1);

namespace App\Entity\Order\Resources\Tests\Functional\Controller\Get;

use App\Entity\Order\Controller\Api\V1\Order\Get\Controller;
use App\Entity\Order\Resources\Tests\OrderTestKernel;

/**
 * @see Controller
 */
class ControllerTest extends OrderTestKernel
{
    public function testSuccess(): void
    {
        $this->requester()->get(Controller::class,["order_id" => $this->getOrderId()]);
    }
}