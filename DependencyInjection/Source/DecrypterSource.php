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

final class DecrypterSource implements SourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'decrypters';
    }

    /**
     * {@inheritdoc}
     */
    public function createService($name, array $config, ContainerBuilder $container)
    {
        $service_id = sprintf('jose.decrypter.%s', $name);
        $definition = new Definition('Jose\Decrypter');
        $definition->setFactory([
            new Reference('jose.factory.service'),
            'createDecrypter',
        ]);
        $definition->setArguments([
            $config['key_encryption_algorithms'],
            $config['content_encryption_algorithms'],
            $config['compression_methods'],
            null === $config['logger'] ? null : new Reference($config['logger']),
        ]);

        $container->setDefinition($service_id, $definition);
    }

    /**
     * {@inheritdoc}
     */
    public function addConfigurationSection(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('decrypters')
            ->useAttributeAsKey('name')
            ->prototype('array')
            ->children()
            ->arrayNode('key_encryption_algorithms')->isRequired()->prototype('scalar')->end()->end()
            ->arrayNode('content_encryption_algorithms')->isRequired()->prototype('scalar')->end()->end()
            ->arrayNode('compression_methods')->defaultValue(['DEF'])->prototype('scalar')->end()->end()
            ->scalarNode('logger')->defaultNull()->end()
            ->booleanNode('create_decrypter')->defaultTrue()->end()
            ->end()
            ->end()
            ->end()
            ->end();
    }
}
