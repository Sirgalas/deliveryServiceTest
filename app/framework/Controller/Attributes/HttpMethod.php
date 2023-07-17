<?php

declare(strict_types=1);

namespace App\Framework\Controller\Attributes;

enum HttpMethod
{
    case GET;
    case POST;
    case OPTIONS;
    case PATCH;
    case PUT;
    case DELETE;
}