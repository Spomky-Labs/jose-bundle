<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2016 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\DependencyInjection\Source\JWKSetSource;

use SpomkyLabs\JoseBundle\DependencyInjection\Source\AbstractSource;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class JWKSet extends AbstractSource implements JWKSetSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function createDefinition(ContainerBuilder $container, array $config)
    {
        $definition = new Definition('Jose\Object\JWKSet');
        $definition->setFactory([
            new Reference('jose.factory.jwk'),
            'createFromValues',
        ]);
        $definition->setArguments([
            json_decode($config['value'], true),
        ]);

        return $definition;
    }

    /**
     * {@inheritdoc}
     */
    public function getKeySet()
    {
        return 'jwkset';
    }

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(NodeDefinition $node)
    {
        parent::addConfiguration($node);
        $node
            ->children()
                ->scalarNode('value')->isRequired()->end()
            ->end();
    }
}
