<?php

declare(strict_types=1);

namespace App\Order\Controller\Api\V1\Delete;

use App\Framework\Controller\AbstractController;
use App\Framework\Controller\Attributes\Delete;
use App\Framework\Controller\Attributes\Requirements\UuidPattern;
use App\Framework\Controller\Attributes\Ruote\TargetResourse\Annotation\TargetResource;
use App\Order\Domain\Entity\Order;
use App\Order\UseCase\Order\Delete\Handler;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[
    Delete(
        path: "/{order_id}/remove",
        name:self::class,
        requirements: [
            "order_id" => UuidPattern::VALUE,
            ]
    ),
    TargetResource(table: Order::TABLE_NAME),
    OA\Delete(
        description: "Удалить заказ.",
        tags: ['Orders.'],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "HTTP OK",
                content:  new OA\JsonContent(ref: '#/components/schemas/empty_response')
            )
        ]
    )
]
class Controller extends AbstractController
{
    /** удалить заказ */
    public function __invoke(string $order_id, Handler $handler): JsonResponse
    {
        $handler->handle($order_id,true);
        return $this->json();
    }
}