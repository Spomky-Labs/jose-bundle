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

class Configuration implements ConfigurationInterface
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
                ->scalarNode('jwk_manager')->defaultValue('jose.jwk_manager.default')->cannotBeEmpty()->end()
                ->scalarNode('jwkset_manager')->defaultValue('jose.jwkset_manager.default')->cannotBeEmpty()->end()
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
                ->arrayNode('storage')
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
        $supportedKeyTypes = ['file', 'jwk', 'jwkset'];

        $node
            ->treatNullLike([])
            ->children()
                ->arrayNode('keys')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                    ->validate()
                        ->ifTrue(function($v) { return 'file' !== $v['type'] && !empty($v['passphrase']); })
                        ->thenInvalid('"passphrase" parameter is only available using type "file"')
                    ->end()
                    ->children()
                        ->scalarNode('type')
                            ->isRequired()
                            ->validate()
                                ->ifNotInArray($supportedKeyTypes)
                                ->thenInvalid('The supported key types are "%s" is not supported. Please choose one of '.json_encode($supportedKeyTypes))
                            ->end()
                        ->end()
                        ->scalarNode('value')->defaultNull()->end()
                        ->scalarNode('passphrase')->defaultNull()->end()
                        ->booleanNode('load_public_key')->defaultTrue()->end()
                        ->booleanNode('shared')->defaultFalse()->end()
                        ->arrayNode('key_ops')
                        ->prototype('scalar')->end()
                            ->treatNullLike([])
                            ->validate()
                                ->ifTrue(function($v) use ($supportedKeyOps) { return 0 !== count(array_diff($v, $supportedKeyOps)); })
                                ->thenInvalid('Unsupported key operation. Please unset the configuration entry or set a list with the following possible values: '.json_encode($supportedKeyOps))
                            ->end()
                        ->end()
                        ->scalarNode('alg')->defaultNull()->end()
                        ->scalarNode('use')
                            ->defaultNull()
                            ->validate()
                                ->ifNotInArray($supportedUsages)
                                ->thenInvalid('The value "%s" is not a valid. Please choose one of null or '.json_encode($supportedUsages))
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
