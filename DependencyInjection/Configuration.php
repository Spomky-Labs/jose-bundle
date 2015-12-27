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

        $rootNode
            ->children()
                ->scalarNode('server_name')
                    ->info('The audience claim ("aud")')
                    ->cannotBeEmpty()
                    ->defaultNull()
                ->end()
                ->arrayNode('compression_methods')
                    ->info('A list of enabled compression methods. Supported methods are: "DEF" (recommended), "GZ" and "ZLIB".')
                    ->prototype('scalar')
                    ->end()
                    ->treatNullLike([])
                ->end()
                ->arrayNode('storage')
                    ->addDefaultsIfNotSet()
                    ->validate()
                        ->ifTrue(function ($value) {
                            return true === $value['enabled'] && null !== $value['class'] && !class_exists($value['class']);
                        })
                        ->thenInvalid('The class does not exist')
                    ->end()
                    ->children()
                        ->booleanNode('enabled')
                            ->info('If true, the storage is used and "jti" header parameter is added.')
                            ->defaultFalse()
                        ->end()
                        ->scalarNode('manager')
                            ->info('The "jot" manager.')
                            ->defaultValue('jose.jot_manager.default')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('class')
                            ->info('The "jot" class.')
                            ->defaultNull()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
