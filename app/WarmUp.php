<?php

declare(strict_types=1);

namespace App;

use Framework\DI;
use Framework\Routes;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class WarmUp
{
    private const CONFIG_PATH = '/etc/di';
    private const DI_CONTAINER_PATH = '/var/cache/container.php';
    private const ROUTES_CACHE_URL_MATCHER_PATH = '/var/cache/routes_matcher.php';

    public static function warmUpDI(bool $isForce = false): void
    {
        if (!$isForce && file_exists(dirname(__DIR__) . self::DI_CONTAINER_PATH)) {
            return;
        }

        $di = new DI(
            dirname(__DIR__) . self::CONFIG_PATH,
            dirname(__DIR__) . self::DI_CONTAINER_PATH,
            true,
        );

        $di->saveContainer(
            $di->buildContainer()
        );
    }

    public static function warmUpRoutes(bool $isForce = false): ContainerBuilder
    {
        $routes = (new Routes(
            dirname(__DIR__) . self::CONFIG_PATH,
            dirname(__DIR__) . self::ROUTES_CACHE_URL_MATCHER_PATH,
        ));

        if (!$isForce && file_exists(dirname(__DIR__) . self::ROUTES_CACHE_URL_MATCHER_PATH)) {
            return $routes->restoreRoutes();
        }

        return $routes->warmUpRoutes();
    }
}
