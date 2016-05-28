<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2016 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\DependencyInjection\Source;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class JWTCreatorSource implements SourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'jwt_creators';
    }

    /**
     * {@inheritdoc}
     */
    public function createService($name, array $config, ContainerBuilder $container)
    {
        $service_id = sprintf('jose.jwt_creator.%s', $name);
        $definition = new Definition('Jose\JWTCreator');
        $definition->setFactory([
            new Reference('jose.factory.service'),
            'createJWTCreator',
        ]);
        $definition->setArguments([
            new Reference($config['signer']),
            null === $config['encrypter'] ? null : new Reference($config['encrypter']),
        ]);

        $container->setDefinition($service_id, $definition);
    }

    /**
     * {@inheritdoc}
     */
    public function addConfigurationSection(ArrayNodeDefinition $node)
    {
        $node->children()
                ->arrayNode('jwt_creators')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('signer')->isRequired()->end()
                            ->scalarNode('encrypter')->defaultNull()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
