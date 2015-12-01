<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class JWKFinderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('jose.jwk_finder_manager')) {
            return;
        }

        $definition = $container->getDefinition('jose.jwk_finder_manager');

        $taggedServices = $container->findTaggedServiceIds('jose_jwk_finder');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addJWKFinder', [new Reference($id)]);
        }
    }
}
