<?php

declare(strict_types=1);

namespace App\Framework\Controller\Attributes;

use Symfony\Component\Routing\Annotation\Route;

#[\Attribute]
class Patch extends Route
{
    public function getMethods(): array
    {
        return [HttpMethod::PATCH->name];
    }
}