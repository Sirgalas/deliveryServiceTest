<?php

declare(strict_types=1);

namespace App\Order\Controller\Api\V1\Add;

use App\Framework\Controller\AbstractController;
use App\Framework\Controller\Attributes\Post;
use App\Order\UseCase\Order\Add\Command;
use App\Order\UseCase\Order\Add\Handler;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Controller extends AbstractController
{
    #[

        Post(
            path: '/add',
            name: self::class,
        ),
        OA\Post(
            description: 'Создать страницу.',
            requestBody: new OA\RequestBody(
                content: new OA\JsonContent(ref: new Model(type: Command::class, groups: ['default']))
            ),
            tags: ['Orders.'],
            responses: [
                new OA\Response(
                    response: Response::HTTP_OK,
                    description: 'HTTP OK',
                    content: new OA\JsonContent(
                        properties: [
                            new OA\Property(
                                property: 'data',
                                properties: [
                                    new OA\Property(property: 'id', description: 'id страницы', type: 'string'),
                                ],
                            ),
                            new OA\Property(property: 'errors', type: 'array', items: new OA\Items(type: 'string')),
                            new OA\Property(property: 'meta', type: 'object'),
                        ],
                    )
                ),
            ]
        ),
    ]
    public function __invoke(Request $request, Handler $handler): JsonResponse
    {
        $command = new Command($request->request->all());
        $this->validate($command);
        return $this->json(['id' => $handler->handle($command)->getId()->toRfc4122()]);
    }
}