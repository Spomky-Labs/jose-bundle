<?php

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
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->alias);

        $supportedSerializationModes = array('full', 'compact', 'flattened');

        $this->addKeySection($rootNode);
        $this->addKeySetSection($rootNode);
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
                ->scalarNode('signer')->defaultValue('spomky_jose.signer.default')->cannotBeEmpty()->end()
                ->scalarNode('loader')->defaultValue('spomky_jose.loader.default')->cannotBeEmpty()->end()
                ->scalarNode('encrypter')->defaultValue('spomky_jose.encrypter.default')->cannotBeEmpty()->end()
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

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addKeySection(ArrayNodeDefinition $node)
    {
        $supportedKeyTypes = array('x5c', 'jwk');

        $node
            ->fixXmlConfig('key')
            ->treatNullLike(array())
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
                        ->scalarNode('value')->defaultNull()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addKeySetSection(ArrayNodeDefinition $node)
    {
        $supportedKeySetTypes = array('x5c', 'x5u', 'jwkset');

        $node
            ->fixXmlConfig('keyset')
            ->treatNullLike(array())
            ->children()
                ->arrayNode('keysets')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                    ->children()
                        ->scalarNode('type')
                            ->isRequired()
                            ->validate()
                                ->ifNotInArray($supportedKeySetTypes)
                                ->thenInvalid('The supported keyset types are "%s" is not supported. Please choose one of '.json_encode($supportedKeySetTypes))
                            ->end()
                        ->end()
                        ->scalarNode('value')->defaultNull()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
