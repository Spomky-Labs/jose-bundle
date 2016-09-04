<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2016 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\DependencyInjection\Source\JWKSource;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class KeyFile implements JWKSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(ContainerBuilder $container, $id, array $config)
    {
        $definition = new Definition('Jose\Object\JWK');
        $definition->setFactory([
            new Reference('jose.factory.jwk'),
            'createFromKeyFile',
        ]);
        $definition->setArguments([
            $config['path'],
            $config['password'],
            $config['additional_values'],
        ]);
        $definition->setPublic($config['is_public']);

        $container->setDefinition($id, $definition);
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return 'file';
    }

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(NodeDefinition $node)
    {
        $node
            ->children()
                ->booleanNode('is_public')
                    ->info('If true, the service will be public, else private.')
                    ->defaultTrue()
                ->end()
                ->scalarNode('path')->isRequired()->end()
                ->scalarNode('password')->defaultNull()->end()
                ->arrayNode('additional_values')
                    ->defaultValue([])
                    ->useAttributeAsKey('key')
                    ->prototype('variable')->end()
                ->end()
            ->end();
    }
}
