<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\DependencyInjection\JWKSource;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class KeyFile implements JWKSourceInterface
{
    /**
     * {@inheritDoc}
     */
    public function create(ContainerBuilder $container, $id, array $config)
    {
        $service_id = sprintf('jose.key.%s', $id);

        $definition = new Definition('Jose\Object\JWK');
        $definition->setFactory([
            new Reference('jose.factory.jwk'),
            'createFromKeyFile'
        ]);
        $definition->setArguments([
            $config['path'],
            $config['password'],
            $config['additional_values']
        ]);

        $container->setDefinition($service_id, $definition);
    }

    /**
     * {@inheritDoc}
     */
    public function getKey()
    {
        return 'file';
    }

    /**
     * {@inheritDoc}
     */
    public function addConfiguration(NodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('path')->isRequired()->end()
                ->scalarNode('password')->defaultNull()->end()
                ->arrayNode('additional_values')
                    ->defaultValue([])
                    ->useAttributeAsKey('key')
                    ->prototype('variable')->end()
                ->end()
            ->end()
        ;
    }
}
