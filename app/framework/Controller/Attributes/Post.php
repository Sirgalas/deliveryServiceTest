<?php

declare(strict_types=1);

namespace App\Framework\Controller\Attributes;

use Symfony\Component\Routing\Annotation\Route;

#[\Attribute]
class Post extends Route
{
    public function getMethods(): array
    {
        return [HttpMethod::POST->name];
    }
}