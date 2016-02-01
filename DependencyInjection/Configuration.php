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
    /**
     * @var \SpomkyLabs\JoseBundle\DependencyInjection\JWKSource\JWKSourceInterface[]
     */
    private $jwk_sources;

    /**
     * @var string
     */
    private $alias;

    /**
     * Configuration constructor.
     *
     * @param string                                                                    $alias
     * @param \SpomkyLabs\JoseBundle\DependencyInjection\JWKSource\JWKSourceInterface[] $jwk_sources
     */
    public function __construct($alias, array $jwk_sources)
    {
        $this->alias = $alias;
        $this->jwk_sources = $jwk_sources;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->alias);

        $this->addJWKSourcesSection($rootNode, $this->jwk_sources);

        $rootNode
            ->children()
                ->arrayNode('compression_methods')
                    ->info('A list of enabled compression methods. Supported methods are: "DEF" (recommended), "GZ" and "ZLIB".')
                    ->prototype('scalar')
                    ->end()
                    ->treatNullLike([])
                ->end()
                ->arrayNode('key_sets')
                    ->defaultValue([])
                    ->useAttributeAsKey('key')
                    ->prototype('array')
                        ->prototype('scalar')
                        ->end()
                    ->end()
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

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition          $node
     * @param \SpomkyLabs\JoseBundle\DependencyInjection\JWKSource\JWKSourceInterface[] $jwk_sources
     */
    private function addJWKSourcesSection(ArrayNodeDefinition $node, array $jwk_sources)
    {
        $sourceNodeBuilder = $node
            ->fixXmlConfig('source')
            ->children()
            ->arrayNode('keys')
            ->useAttributeAsKey('name')
            ->prototype('array')
            ->performNoDeepMerging()
            ->children()
        ;
        foreach ($jwk_sources as $name => $source) {
            $sourceNode = $sourceNodeBuilder->arrayNode($name)->canBeUnset();
            $source->addConfiguration($sourceNode);
        }
    }
}
