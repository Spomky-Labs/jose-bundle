<?php

namespace SpomkyLabs\JoseBundle\DependencyInjection;

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
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->alias);

        $supportedSerializationModes = array('compact', 'flattened');

        $rootNode
            ->children()
                ->scalarNode('server_name')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('serialization_mode')
                    ->validate()
                        ->ifNotInArray($supportedSerializationModes)
                        ->thenInvalid('The serialization mode "%s" is not supported. Please choose one of '.json_encode($supportedSerializationModes))
                    ->end()
                    ->cannotBeEmpty()
                    ->defaultValue('compact')
                ->end()
                ->scalarNode('jwa_manager')->defaultValue('spomky_jose.jwa_manager.default')->cannotBeEmpty()->end()
                ->scalarNode('jwt_manager')->defaultValue('spomky_jose.jwt_manager.default')->cannotBeEmpty()->end()
                ->scalarNode('jwk_manager')->defaultValue('spomky_jose.jwk_manager.default')->cannotBeEmpty()->end()
                ->scalarNode('jwkset_manager')->defaultValue('spomky_jose.jwkset_manager.default')->cannotBeEmpty()->end()
                ->scalarNode('jwt_class')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('jws_class')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('jwe_class')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('jwk_class')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('jwkset_class')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('compression_manager')->defaultValue('spomky_jose.compression_manager.default')->cannotBeEmpty()->end()
                ->arrayNode('algorithms')->prototype('scalar')->end()->treatNullLike(array())->end()
                ->arrayNode('compression_methods')->prototype('scalar')->end()->treatNullLike(array())->end()
            ->end();

        return $treeBuilder;
    }
}
