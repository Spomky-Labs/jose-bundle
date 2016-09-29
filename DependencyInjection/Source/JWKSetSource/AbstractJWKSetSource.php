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

use Symfony\Component\Config\Definition\Builder\NodeDefinition;

abstract class AbstractJWKSetSource
{
    /**
     * {@inheritdoc}
     */
    public function create(ContainerBuilder $container, $id, array $config)
    {
        parent::create($container, $id, $config);
        
        if ($config['is_shared']) {
            $controller_definition = new Definition('SpomkyLabs\JoseBundle\Controller\JWKSetController');
            $controller_definition->setFactory([new Reference('jose.controller.jwkset_controllery_factory'), 'createJWKSetController']);
            $controller_definition->setArguments([new Reference($id]);
            $container->setDefinition('jose.controller.'.$id, $controller_definition);
        }
    }
    
    /**
     * @param \Symfony\Component\Config\Definition\Builder\NodeDefinition $node
     */
    public function addConfiguration(NodeDefinition $node)
    {
        parent::addConfiguration($node);
        $node
            ->children()
                ->booleanNode('is_shared')
                    ->info('If true, a controller will be created to ease the JWKSet to be shared.')
                    ->defaultFalse()
                ->end()
            ->end();
    }
}
