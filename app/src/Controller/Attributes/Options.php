<?php

declare(strict_types=1);

namespace App\Controller\Attributes;

use Symfony\Component\Routing\Annotation\Route;

#[\Attribute]
class Options extends Route
{
    public function getMethods()
    {
        return [HttpMethod::OPTIONS->name];
    }
}