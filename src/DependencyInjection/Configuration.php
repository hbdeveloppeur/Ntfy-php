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
                    ->children()
                        ->scalarNode('error')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('log')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
