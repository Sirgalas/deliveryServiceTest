<?php

declare(strict_types=1);

namespace App\Framework\Security\Cms;

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routing): void {

    $routing
        ->import(\dirname(__DIR__, 2) . '/Controller/Api/V1', 'annotation')
        ->prefix('/api/v1/orders')
    ;
};
