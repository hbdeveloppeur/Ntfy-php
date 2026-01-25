<?php

namespace Notify\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('ntfy');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('channels')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('error')->defaultValue('%env(NTFY_ERROR_CHANNEL)%')->end()
                        ->scalarNode('log')->defaultValue('%env(NTFY_LOG_CHANNEL)%')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
