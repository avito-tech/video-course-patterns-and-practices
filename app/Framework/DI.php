<?php

declare(strict_types=1);

namespace Framework;

use RuntimeException;
use Symfony\Component\Config;
use Symfony\Component\DependencyInjection;
use Throwable;

class DI
{
    public function __construct(
        private readonly string $configPath,
        private readonly string $containerPath,
        private readonly bool $isDebugMode,
    ) {
    }

    public function saveContainer(DependencyInjection\ContainerBuilder $containerBuilder): void
    {
        $dump = (new DependencyInjection\Dumper\PhpDumper($containerBuilder))
            ->dump([
                'namespace' => 'DIContainer',
                'class' => 'CachedContainer',
                'debug' => $this->isDebugMode,
            ]);

        (new Config\ConfigCache($this->containerPath, $this->isDebugMode))
            ->write($dump, $containerBuilder->getResources());
    }

    public function buildContainer(): DependencyInjection\ContainerBuilder
    {
        $container = new DependencyInjection\ContainerBuilder();

        $this->loadDi($container);

        try {
            $container->compile();
        } catch (Throwable $e) {
            throw new RuntimeException('Cannot compile DI container. Info: ' . $e->getMessage(), previous: $e);
        }

        return $container;
    }

    private function loadDi(DependencyInjection\ContainerBuilder $container): void
    {
        try {
            $fileLoader = new DependencyInjection\Loader\YamlFileLoader(
                $container,
                new Config\FileLocator($this->configPath)
            );
            $fileLoader->load('services.yml');
        } catch (Throwable $e) {
            throw new RuntimeException('Cannot load services config file. Info: ' . $e->getMessage(), previous: $e);
        }
    }
}
