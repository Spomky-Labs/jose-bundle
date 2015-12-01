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
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;

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

        $this->addKeySection($rootNode);
        $this->addStorageSection($rootNode);
        $rootNode
            ->children()
                ->booleanNode('use_controller')->defaultTrue()->end()
                ->scalarNode('server_name')->cannotBeEmpty()->end()
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

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addKeySection(ArrayNodeDefinition $node)
    {
        $supportedKeyOps = [
            'sign',
            'verify',
            'encrypt',
            'decrypt',
            'wrapKey',
            'unwrapKey',
            'deriveKey',
            'deriveBits',
        ];
        $supportedUsages = ['sig', 'enc'];

        $node
            ->treatNullLike([])
            ->children()
                ->arrayNode('keys')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                    ->validate()
                        ->ifTrue(function($v) { return !array_key_exists('file',$v) && !empty($v['passphrase']); })
                        ->thenInvalid('"passphrase" parameter is only available using a key/certificate from a file')
                    ->end()
                    ->children()
                        ->scalarNode('certificate')->defaultNull()->end()
                        ->scalarNode('file')->defaultNull()->end()
                        ->scalarNode('jwk')->defaultNull()->end()
                        ->scalarNode('jwkset')->defaultNull()->end()
                        ->arrayNode('values')
                            ->useAttributeAsKey('key')
                            ->prototype('variable')
                                ->validate()
                                ->always(function ($v) {
                                    if (is_string($v) || is_array($v)) {
                                        return $v;
                                    }
                                    throw new InvalidTypeException();
                                })
                                ->end()
                            ->end()
                            ->treatNullLike([])
                        ->end()
                        ->scalarNode('passphrase')->defaultNull()->end()
                        ->booleanNode('load_public_key')->defaultTrue()->end()
                        ->booleanNode('shared')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end();
    }
}
