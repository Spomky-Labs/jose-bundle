<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2016 Spomky-Labs
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
     * @var \SpomkyLabs\JoseBundle\DependencyInjection\JWKSetSource\JWKSetSourceInterface[]
     */
    private $jwk_set_sources;

    /**
     * @var string
     */
    private $alias;

    /**
     * Configuration constructor.
     *
     * @param string                                                                          $alias
     * @param \SpomkyLabs\JoseBundle\DependencyInjection\JWKSource\JWKSourceInterface[]       $jwk_sources
     * @param \SpomkyLabs\JoseBundle\DependencyInjection\JWKSetSource\JWKSetSourceInterface[] $jwk_set_sources
     */
    public function __construct($alias, array $jwk_sources, array $jwk_set_sources)
    {
        $this->alias = $alias;
        $this->jwk_sources = $jwk_sources;
        $this->jwk_set_sources = $jwk_set_sources;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->alias);

        $this->addJWKSourcesSection($rootNode, $this->jwk_sources);
        $this->addJWKSetSourcesSection($rootNode, $this->jwk_set_sources);

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
            ->children();
        foreach ($jwk_sources as $name => $source) {
            $sourceNode = $sourceNodeBuilder->arrayNode($name)->canBeUnset();
            $source->addConfiguration($sourceNode);
        }
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition                $node
     * @param \SpomkyLabs\JoseBundle\DependencyInjection\JWKSetSource\JWKSetSourceInterface[] $jwk_set_sources
     */
    private function addJWKSetSourcesSection(ArrayNodeDefinition $node, array $jwk_set_sources)
    {
        $sourceNodeBuilder = $node
            ->fixXmlConfig('source')
            ->children()
            ->arrayNode('key_sets')
            ->useAttributeAsKey('name')
            ->prototype('array')
            ->performNoDeepMerging()
            ->children();
        foreach ($jwk_set_sources as $name => $source) {
            $sourceNode = $sourceNodeBuilder->arrayNode($name)->canBeUnset();
            $source->addConfiguration($sourceNode);
        }
    }
}
