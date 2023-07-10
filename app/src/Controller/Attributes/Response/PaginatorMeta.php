<?php

declare(strict_types=1);

namespace App\Controller\Attributes\Response;

use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;
#[\Attribute]
class PaginatorMeta
{
    #[
        Groups(['default']),
        OA\Property(description: 'Всего.', type: 'integer')
    ]
    public int $total;

    #[
        Groups(['default']),
        OA\Property(description: 'Страница.', type: 'integer')
    ]
    public int $page;

    #[
        Groups(['default']),
        OA\Property(description: 'Количество на странице.', type: 'integer')
    ]
    public int $page_size;
}