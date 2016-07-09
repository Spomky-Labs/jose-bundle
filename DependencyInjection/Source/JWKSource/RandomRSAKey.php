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

use Assert\Assertion;
use Jose\Factory\JWKFactory;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RandomRSAKey extends RandomKey
{
    /**
     * {@inheritdoc}
     */
    protected function createNewKey(array $config)
    {
        $size = $config['size'];
        $values = $config['additional_values'];
        
        return JWKFactory::createRSAKey($size, $values)->getAll();
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return 'rsa';
    }

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(NodeDefinition $node)
    {
        $node
            ->children()
                ->integerNode('size')->isRequired()->min(384)->end()
            ->end();
        parent::addConfiguration($node);
    }
}
