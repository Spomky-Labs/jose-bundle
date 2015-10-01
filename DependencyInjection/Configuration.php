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

        $this->addJotSection($rootNode);
        $this->addKeySection($rootNode);
        $rootNode
            ->children()
                ->scalarNode('server_name')->isRequired()->cannotBeEmpty()->end()
                ->booleanNode('use_controller')->defaultTrue()->end()
                ->scalarNode('jwt_manager')->defaultValue('jose.jwt_manager.default')->cannotBeEmpty()->end()
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
    private function addJotSection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('jot')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('headers')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('jku')->defaultFalse()->end()
                                ->booleanNode('jwk')->defaultFalse()->end()
                                ->booleanNode('kid')->defaultTrue()->end()
#                                ->booleanNode('x5u')->defaultFalse()->end()
                                ->booleanNode('x5c')->defaultFalse()->end()
                                ->booleanNode('x5t')->defaultFalse()->end()
                                ->booleanNode('x5t#256')->defaultFalse()->end()
                                ->arrayNode('crit')
                                    ->prototype('scalar')->end()
                                    ->treatNullLike([])
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('claims')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('iss')->defaultTrue()->end()
                                ->booleanNode('nbf')->defaultTrue()->end()
                                ->booleanNode('iat')->defaultTrue()->end()
                                ->scalarNode('lifetime')->defaultValue('5 min')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addKeySection(ArrayNodeDefinition $node)
    {
        $supportedUsages = ['sig', 'enc'];
        $supportedKeyTypes = ['rsa', 'ecc', 'shared', 'direct', 'jwk', 'jwkset'];

        $node
            ->treatNullLike([])
            ->children()
                ->arrayNode('keys')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                    ->children()
                        ->scalarNode('type')
                            ->isRequired()
                            ->validate()
                                ->ifNotInArray($supportedKeyTypes)
                                ->thenInvalid('The supported key types are "%s" is not supported. Please choose one of '.json_encode($supportedKeyTypes))
                            ->end()
                        ->end()
                        ->scalarNode('private_file')->cannotBeEmpty()->end()
                        ->scalarNode('public_file')->cannotBeEmpty()->end()
                        ->scalarNode('value')->end()
                        ->scalarNode('passphrase')->defaultNull()->end()
                        ->arrayNode('key_ops')
                        ->prototype('scalar')->end()
                            ->treatNullLike([])
                            /*->validate()
                                ->ifNotInArray($supportedKeyOps)
                                ->thenInvalid('The value "%s" is not a valid. Please choose one of null or '.json_encode($supportedKeyOps))
                            ->end()*/
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
