<?php

declare(strict_types=1);

namespace App\Framework\Controller\Attributes;

use Symfony\Component\Routing\Annotation\Route;

#[\Attribute]
class Options extends Route
{
    public function getMethods()
    {
        return [HttpMethod::OPTIONS->name];
    }
}