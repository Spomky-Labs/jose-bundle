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

abstract class RandomKey implements JWKSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(ContainerBuilder $container, $id, array $config)
    {
        $definition = new Definition('Jose\Object\RotatableJWK');
        $definition->setFactory([
            new Reference('jose.factory.jwk'),
            'createRotatableKey',
        ]);

        $key_config = $this->getKeyConfig($config);

        $definition->setArguments([$config['storage_path'], $key_config, $config['ttl']]);
        $container->setDefinition($id, $definition);
    }

    /**
     * @param array $config
     *
     * @return array
     */
    abstract protected function getKeyConfig(array $config);

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(NodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('storage_path')->isRequired()->end()
                ->integerNode('ttl')->defaultValue(0)->min(0)->end()
                ->arrayNode('additional_values')
                    ->defaultValue([])
                    ->useAttributeAsKey('key')
                    ->prototype('variable')->end()
                ->end()
            ->end();
    }
}
