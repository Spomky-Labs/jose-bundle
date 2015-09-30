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

        $this->addJotSection($rootNode);
        $this->addKeySection($rootNode);
        $rootNode
            ->children()
                ->scalarNode('server_name')->isRequired()->cannotBeEmpty()->end()
                ->booleanNode('use_controller')->defaultTrue()->end()
                ->scalarNode('jose')->defaultValue('sl_jose.jose.default')->cannotBeEmpty()->end()
                ->scalarNode('jwa_manager')->defaultValue('sl_jose.jwa_manager.default')->cannotBeEmpty()->end()
                ->scalarNode('jwt_manager')->defaultValue('sl_jose.jwt_manager.default')->cannotBeEmpty()->end()
                ->scalarNode('jwk_manager')->defaultValue('sl_jose.jwk_manager.default')->cannotBeEmpty()->end()
                ->scalarNode('jwkset_manager')->defaultValue('sl_jose.jwkset_manager.default')->cannotBeEmpty()->end()
                ->scalarNode('signer')->defaultValue('sl_jose.signer.default')->cannotBeEmpty()->end()
                ->scalarNode('loader')->defaultValue('sl_jose.loader.default')->cannotBeEmpty()->end()
                ->scalarNode('encrypter')->defaultValue('sl_jose.encrypter.default')->cannotBeEmpty()->end()
                ->scalarNode('jwkset_manager')->defaultValue('sl_jose.jwkset_manager.default')->cannotBeEmpty()->end()
                ->scalarNode('jwt_class')->defaultValue('\SpomkyLabs\JoseBundle\Entity\JWT')->cannotBeEmpty()->end()
                ->scalarNode('jws_class')->defaultValue('\SpomkyLabs\JoseBundle\Entity\JWS')->cannotBeEmpty()->end()
                ->scalarNode('jwe_class')->defaultValue('\SpomkyLabs\JoseBundle\Entity\JWE')->cannotBeEmpty()->end()
                ->scalarNode('jwk_class')->defaultValue('\SpomkyLabs\JoseBundle\Entity\JWK')->cannotBeEmpty()->end()
                ->scalarNode('jwkset_class')->defaultValue('\SpomkyLabs\JoseBundle\Entity\JWKSet')->cannotBeEmpty()->end()
                ->scalarNode('compression_manager')->defaultValue('sl_jose.compression_manager.default')->cannotBeEmpty()->end()
                ->arrayNode('algorithms')->prototype('scalar')->end()->treatNullLike(array())->end()
                ->arrayNode('compression_methods')->prototype('scalar')->end()->treatNullLike(array())->end()
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
                                    ->treatNullLike(array())
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
        $supportedUsages = array('sig', 'enc');
        $supportedKeyTypes = array('rsa', 'ecc'/*, 'jwk', 'jwkset'*/);

        $node
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
                        ->append($this->getPublicKeyConfiguration())
                        ->append($this->getPrivateKeyConfiguration())
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
            ->end()
        ;
    }

    private function getPublicKeyConfiguration()
    {
        $supportedKeyOps = array('sign', 'verify', 'decrypt', 'encrypt', 'wrapKey', 'unwrapKey', 'deriveKey', 'deriveBits');
        $builder = new TreeBuilder();
        $node = $builder->root('public');

        $node
            ->isRequired()
            ->children()
                ->scalarNode('file')->cannotBeEmpty()->end()
                ->arrayNode('key_ops')
                    ->prototype('scalar')->end()
                    ->treatNullLike(array())
                    /*->validate()
                        ->ifNotInArray($supportedKeyOps)
                        ->thenInvalid('The value "%s" is not a valid. Please choose one of null or '.json_encode($supportedKeyOps))
                    ->end()*/
                ->end()
            ->end();

        return $node;
    }

    private function getPrivateKeyConfiguration()
    {
        $supportedKeyOps = array('sign', 'verify', 'decrypt', 'encrypt', 'wrapKey', 'unwrapKey', 'deriveKey', 'deriveBits');
        $builder = new TreeBuilder();
        $node = $builder->root('private');

        $node
            ->isRequired()
            ->children()
                ->scalarNode('file')->cannotBeEmpty()->end()
                ->scalarNode('passphrase')->defaultNull()->end()
                ->arrayNode('key_ops')
                    ->prototype('scalar')->end()
                    ->treatNullLike(array())
                    /*->validate()
                        ->ifNotInArray($supportedKeyOps)
                        ->thenInvalid('The value "%s" is not a valid. Please choose one of null or '.json_encode($supportedKeyOps))
                    ->end()*/
                ->end()
            ->end();

        return $node;
    }
}
