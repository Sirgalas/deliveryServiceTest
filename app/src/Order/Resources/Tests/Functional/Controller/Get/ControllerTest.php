<?php

declare(strict_types=1);

namespace App\Order\Resources\Tests\Functional\Controller\Get;

use App\Order\Controller\Api\V1\Get\Controller;
use App\Order\Resources\Tests\OrderTestKernel;

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