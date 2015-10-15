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

class KeysCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('jose.jwkset_manager.default')) {
            return;
        }

        $definition = $container->getDefinition('jose.jwkset_manager.default');

        $keys = $container->getParameter('jose.keys');

        foreach ($keys as $id=>$key) {
            switch ($key['type']) {
                case 'file':
                    $additional_data = [
                    ];
                    foreach(['alg'=>'alg', 'use'=>'use', 'key_ops'=>'key_ops'] as  $k=>$v) {
                        if (!empty($key[$k])) {
                            $additional_data[$v] = $key[$k];
                        }
                    }
                    $definition->addMethodCall('loadKeyFromFile', [$id, $key['file'], $key['passphrase'], $key['shared'], $key['load_public_key'], $additional_data]);
                    break;
                case 'jwk':
                    $definition->addMethodCall('loadKeyFromJWK', [$key['value'], $key['shared']]);
                    break;
                case 'jwkset':
                    $definition->addMethodCall('loadKeyFromJWKSet', [$key['value'], $key['shared']]);
                    break;
            }
        }
    }
}
