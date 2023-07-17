<?php

declare(strict_types=1);

namespace App\Order;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $configurator->extension(
        'doctrine',
        [
            'orm' => [
                'mappings' => [
                    'App\Order\Domain\Entity' => [
                        'type' => 'attribute',
                        'dir' => \dirname(__DIR__, 2) . '/Domain/Entity',
                        'is_bundle' => false,
                        'prefix' => __NAMESPACE__ . '\Domain\Entity',
                        'alias' => 'Order',
                    ],
                ],
            ],
        ]
    );
    $configurator->extension('framework', ['translator' => ['paths' => [\dirname(__DIR__) . '/translations']]]);

    $configurator->import(__DIR__ . \DIRECTORY_SEPARATOR . 'services.yaml');
};
