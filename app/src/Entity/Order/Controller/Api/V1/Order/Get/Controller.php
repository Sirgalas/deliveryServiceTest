<?php

declare(strict_types=1);

namespace App\Entity\Order\Controller\Api\V1\Order\Get;


use App\Entity\Order\Domain\Entity\Order;
use App\Entity\Order\View\Commands\OneOrderCommand as Command;
use App\Controller\AbstractController;
use App\Entity\Order\View\OrderFetcher;
use App\Controller\Attributes\Get;
use App\Controller\Attributes\Requirements\UuidPattern;
use App\Controller\Attributes\Ruote\TargetResourse\Annotation\TargetResource;
use App\Security\Voter\Admin\Access;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

#[
    Get(
        path: '/{order_id}',
        name: self::class,
        requirements: ['order_id' => UuidPattern::VALUE]
    ),
    TargetResource(table: Order::TABLE_NAME),
    OA\Get(
        description: 'Информация заказа.',
        tags: ['Orders.'],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'HTTP OK',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: new Model(type: Command::class, groups: ['default'])),
                        new OA\Property(property: 'errors', type: 'array', items: new OA\Items(type: 'string')),
                        new OA\Property(property: 'meta', type: 'object'),
                    ],
                )
            ),
        ]
    ),
]
class Controller extends AbstractController
{
    public function __invoke(string $order_id, OrderFetcher $fetcher): JsonResponse
    {
        return $this->json($fetcher->one($order_id));
    }
}