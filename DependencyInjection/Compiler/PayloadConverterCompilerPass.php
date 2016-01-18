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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class PayloadConverterCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('jose.payload_converter_manager')) {
            return;
        }

        $definition = $container->getDefinition('jose.payload_converter_manager');

        $taggedServices = $container->findTaggedServiceIds('jose_payload_converter');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addConverter', [new Reference($id)]);
        }
    }
}
