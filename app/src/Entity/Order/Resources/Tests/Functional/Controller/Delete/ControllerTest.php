<?php

declare(strict_types=1);

namespace App\Entity\Order\Resources\Tests\Functional\Controller\Delete;

use App\Entity\Order\Resources\Tests\OrderTestKernel;
use App\Entity\Order\Controller\Api\V1\Order\Delete\Controller;

/**
 * @see Controller
 */
class ControllerTest extends OrderTestKernel
{
    public function testSuccess(): void {
        $this->requester()->delete(Controller::class,['order_id' => $this->getOrderId()]);
    }
}