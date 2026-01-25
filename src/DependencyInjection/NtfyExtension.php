<?php

namespace Notify\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\Config\FileLocator;

class NtfyExtension extends Extension
{
    public function getAlias(): string
    {
        return 'ntfy';
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('Notify\Adapters\NtfyNotifier');
        $definition->setArgument('$errorChannelId', $config['channels']['error'] ?? '%env(NTFY_ERROR_CHANNEL)%');
        $definition->setArgument('$logChannelId', $config['channels']['log'] ?? '%env(NTFY_LOG_CHANNEL)%');
    }
}
