<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    private $alias;

    /**
     * @param string $alias
     */
    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->alias);

        $this->addStorageSection($rootNode);
        $rootNode
            ->children()
                ->scalarNode('server_name')->cannotBeEmpty()->defaultValue('OAuth2 Server')->end()
                ->arrayNode('algorithms')->prototype('scalar')->end()->treatNullLike([])->end()
                ->arrayNode('compression_methods')->prototype('scalar')->end()->treatNullLike([])->end()
            ->end();

        return $treeBuilder;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addStorageSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('jot')
                    ->children()
                        ->booleanNode('enabled')->defaultFalse()->end()
                        ->scalarNode('manager')->defaultValue('jose.jot_manager.default')->cannotBeEmpty()->end()
                        ->scalarNode('class')->defaultNull()->end()
                    ->end()
                ->end()
            ->end();
    }
}
