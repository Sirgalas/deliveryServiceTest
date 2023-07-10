<?php

declare(strict_types=1);

namespace App\Entity\Order\Controller\Api\V1\Order\Delete;

use App\Controller\AbstractController;
use App\Controller\Attributes\Delete;
use App\Controller\Attributes\Requirements\UuidPattern;
use App\Controller\Attributes\Ruote\TargetResourse\Annotation\TargetResource;
use App\Entity\Order\UseCase\Order\Delete\Handler;
use App\Security\Voter\Admin\Access;
use App\Entity\Order\Domain\Entity\Order;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

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