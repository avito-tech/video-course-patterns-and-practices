<?php

declare(strict_types=1);

namespace Framework;

use RuntimeException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper;
use Symfony\Component\Routing\RouteCollection;
use Throwable;

class Routes
{
    public function __construct(
        private readonly string $configPath,
        private readonly string $cachePathUrlMatcher,
    ) {
    }

    public function restoreRoutes(): DependencyInjection\ContainerBuilder
    {
        return $this->setIntoContainerBuilder(
            $this->getRoutes($this->cachePathUrlMatcher),
        );
    }

    public function warmUpRoutes(): DependencyInjection\ContainerBuilder
    {
        $routeCollection = $this->loadRoutes();

        return $this->setIntoContainerBuilder(
            $this->dumpForUrlMatcher($routeCollection),
        );
    }

    protected function getRoutes(string $cachePath): array
    {
        return json_decode(file_get_contents($cachePath), true, flags: JSON_THROW_ON_ERROR);
    }

    protected function dumpForUrlMatcher(RouteCollection $routeCollection): array
    {
        $compiledUrlMatcherDumper = (new CompiledUrlMatcherDumper($routeCollection))->getCompiledRoutes();
        file_put_contents($this->cachePathUrlMatcher, json_encode($compiledUrlMatcherDumper, JSON_THROW_ON_ERROR));

        return $compiledUrlMatcherDumper;
    }

    protected function loadRoutes(): RouteCollection
    {
        $loader = new YamlFileLoader(
            new FileLocator($this->configPath)
        );

        try {
            $routeCollection = $loader->load('routes.yml');
        } catch (Throwable $e) {
            throw new RuntimeException('Cannot load routes. Info: ' . $e->getMessage(), previous: $e);
        }

        return $routeCollection;
    }

    protected function setIntoContainerBuilder(array $urlMatcher): DependencyInjection\ContainerBuilder
    {
        $containerBuilder = new DependencyInjection\ContainerBuilder();
        $containerBuilder->setParameter('routes_url_matcher', $urlMatcher);
        $containerBuilder->compile();

        return $containerBuilder;
    }
}
