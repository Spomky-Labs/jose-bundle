<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2016 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\DependencyInjection\Compiler;

use Assert\Assertion;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class CompressionCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('jose.compression_manager')) {
            return;
        }

        $loaded = [];
        $definition = $container->getDefinition('jose.compression_manager');

        $taggedServices = $container->findTaggedServiceIds('jose_compression');
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                Assertion::keyExists($attributes, 'alias', sprintf("The compression method '%s' does not have any 'alias' attribute.", $id));
                $loaded[] = $attributes['alias'];
                $definition->addMethodCall('addCompressionAlgorithm', [new Reference($id)]);
            }
        }
    }
}
