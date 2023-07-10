<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private function configureContainer(
        ContainerConfigurator $container,
        LoaderInterface $loader,
        ContainerBuilder $builder
    ): void {
        $appDir = $this->getProjectDir();

        // Global configuration.
        $this->configureGlobalContainer($container, $loader, $builder, "{$appDir}/config");
        // App configuration.
        $container->import("{$appDir}/src/**/Resources/config/config.php", null, true);
        $container->import("{$appDir}/src/Entity/**/Resources/config/config.php", null, true);
  }

    private function configureGlobalContainer(
        ContainerConfigurator $container,
        LoaderInterface $loader,
        ContainerBuilder $builder,
        string $configDir
    ): void {
        $container->import($configDir . '/{packages}/*.yaml');
        $container->import($configDir . '/{packages}/' . $this->environment . '/*.yaml');

        if (is_file($configDir . '/services.yaml')) {
            $container->import($configDir . '/services.yaml');
            $container->import($configDir . '/{services}_' . $this->environment . '.yaml');
        } else {
            $container->import($configDir . '/{services}.php');
        }
    }

    private function configureRoutes(RoutingConfigurator $routes): void
    {
        $appDir = $this->getProjectDir();

        // Global routes.
        $this->configureGlobalRoutes($routes, "{$appDir}/config");
        // App routes.
        $routes->import("{$appDir}/src/Entity/**/Resources/config/routes.php", null, true);
  }

    private function configureGlobalRoutes(RoutingConfigurator $routes, string $configDir): void
    {
        $routes->import($configDir . '/{routes}/' . $this->environment . '/*.yaml');
        $routes->import($configDir . '/{routes}/*.yaml');

        if (is_file($configDir . '/routes.yaml')) {
            $routes->import($configDir . '/routes.yaml');

            return;
        }

        $routes->import($configDir . '/{routes}.php');
    }
}
