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

use Jose\Factory\JWKFactory;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

class RandomOKPKey extends RandomKey
{
    /**
     * {@inheritdoc}
     */
    protected function createNewKey(array $config)
    {
        $curve = $config['curve'];
        $values = $config['additional_values'];

        return JWKFactory::createOKPKey($curve, $values)->getAll();
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return 'okp';
    }

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(NodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('curve')
                    ->validate()
                        ->ifTrue(self::checkCurve())
                        ->thenInvalid('Unsupported curve. Please use "X25519" or "Ed25519".')
                    ->end()
                    ->isRequired()
                ->end()
            ->end();
        parent::addConfiguration($node);
    }

    private static function checkCurve()
    {
        return function ($v) {
            return !in_array($v, ['X25519', 'Ed25519']);
        };
    }
}
