<?php

declare(strict_types=1);

namespace App\Order\Resources\Tests\Functional\Controller\Delete;

use App\Order\Controller\Api\V1\Delete\Controller;
use App\Order\Resources\Tests\OrderTestKernel;

/**
 * @see Controller
 */
class ControllerTest extends OrderTestKernel
{
    public function testSuccess(): void {
        $this->requester()->delete(Controller::class,['order_id' => $this->getOrderId()]);
    }
}