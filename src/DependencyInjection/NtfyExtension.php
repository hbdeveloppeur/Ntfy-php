<?php

namespace Ntfy\DependencyInjection;

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

        $definition = $container->getDefinition('Ntfy\Adapters\Client');
        
        // Pass full configuration arrays
        $definition->setArgument('$errorChannel', $config['channels']['error']);
        $definition->setArgument('$logChannel', $config['channels']['log']);
        $definition->setArgument('$urgentChannel', $config['channels']['urgent']);
        
        // Pass the environment
        $definition->setArgument('$environment', '%kernel.environment%');
    }
}
