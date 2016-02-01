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

/**
 * Factory configuration for the Gaufrette DIC extension
 *
 * @author Antoine Hérault <antoine.herault@gmail.com>
 */
class SourceConfiguration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $treeBuilder
            ->root('jose')
                ->ignoreExtraKeys()
                ->fixXmlConfig('key', 'keys')
                    ->children()
                        ->arrayNode('keys')
                        ->prototype('variable')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
