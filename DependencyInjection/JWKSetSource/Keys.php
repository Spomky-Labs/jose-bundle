<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2016 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\DependencyInjection\JWKSetSource;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class Keys implements JWKSetSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(ContainerBuilder $container, $id, array $config)
    {
        $definition = new Definition('Jose\Object\JWKSet');
        $definition->setFactory([
            new Reference('jose.factory.jwkset'),
            'createFromKeys',
        ]);
        $definition->setArguments([
            $this->fromKeysToReferences($config['id']),
        ]);

        $container->setDefinition($id, $definition);
    }

    /**
     * {@inheritdoc}
     */
    public function getKeySet()
    {
        return 'keys';
    }

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(NodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('id')
                    ->prototype('scalar')
                    ->end()
                    ->isRequired()
                ->end()
            ->end();
    }

    /**
     * @param string[] $keys
     *
     * @return \Symfony\Component\DependencyInjection\Reference[]
     */
    private function fromKeysToReferences(array $keys)
    {
        $result = [];
        foreach ($keys as $key) {
            $result[] = new Reference(sprintf('jose.key.%s', $key));
        }

        return $result;
    }
}
