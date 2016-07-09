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
        if (file_exists($config['storage_path'])) {
            $values = $this->getValuesFromFileContent($config['storage_path'], $config['ttl']);
            if (null === $values) {
                $values = $this->generateKey($config);
            }
        } else {
            $values = $this->generateKey($config);
        }

        $this->createKeyDefinition($container, $id, $values);

    }

    /**
     * @param string   $storage_path
     * @param int|null $ttl
     *
     * @return null|array
     */
    private function getValuesFromFileContent($storage_path, $ttl)
    {
        $content = file_get_contents($storage_path);
        if (false === $content) {
            return;
        }

        $data = json_decode($content, true);
        if (!is_array($data) || !array_key_exists('jwk', $data)) {
            return;
        }

        if (null !== $ttl && (!array_key_exists('expires_at', $data) || time() > $data['expires_at'])) {
            return;
        }

        return $data['jwk'];
    }

    /**
     * @param array $config
     *
     * @return array
     */
    private function generateKey(array $config)
    {
        $values = $this->createNewKey($config);
        $values['kid'] = hash('sha512', random_bytes(64));

        $to_file = [
            'jwk' => $values
        ];
        if (0 !== $config['ttl']) {
            $to_file['expires_at'] = time() + $config['ttl'];
        }

        file_put_contents($config['storage_path'], json_encode($to_file));

        return $values;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    abstract protected function createNewKey(array $config);

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string                                                  $id
     * @param array                                                   $values
     */
    private function createKeyDefinition(ContainerBuilder $container, $id, array $values)
    {
        $definition = new Definition('Jose\Object\JWK');
        $definition->setFactory([
            new Reference('jose.factory.jwk'),
            'createFromValues',
        ]);
        
        $definition->setArguments([$values]);
        $container->setDefinition($id, $definition);
    }

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
