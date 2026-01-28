<?php

namespace Ntfy\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('ntfy');

        $treeBuilder->getRootNode()
            ->children()
                ->booleanNode('silent')->defaultFalse()->end()
                ->arrayNode('channels')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('error')
                            ->beforeNormalization()
                                ->ifString()
                                ->then(fn ($v) => ['id' => $v])
                            ->end()
                            ->children()
                                ->scalarNode('id')->defaultValue('%env(NTFY_ERROR_CHANNEL)%')->end()
                                ->booleanNode('dev_only')->defaultFalse()->end()
                            ->end()
                        ->end()
                        ->arrayNode('log')
                            ->beforeNormalization()
                                ->ifString()
                                ->then(fn ($v) => ['id' => $v])
                            ->end()
                            ->children()
                                ->scalarNode('id')->defaultValue('%env(NTFY_LOG_CHANNEL)%')->end()
                                ->booleanNode('dev_only')->defaultFalse()->end()
                            ->end()
                        ->end()
                        ->arrayNode('urgent')
                            ->beforeNormalization()
                                ->ifString()
                                ->then(fn ($v) => ['id' => $v])
                            ->end()
                            ->children()
                                ->scalarNode('id')->defaultValue('%env(NTFY_URGENT_CHANNEL)%')->end()
                                ->booleanNode('dev_only')->defaultFalse()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
