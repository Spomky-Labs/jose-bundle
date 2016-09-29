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
use Symfony\Component\DependencyInjection\ContainerBuilder;
use SpomkyLabs\JoseBundle\DependencyInjection\Source\AbstractSource;

abstract class AbstractJWKSetSource extends AbstractSource implements JWKSetSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(ContainerBuilder $container, $id, array $config)
    {
        parent::create($container, $id, $config);
        
        if (null !== $config['path']) {
            $controller_definition = new Definition('SpomkyLabs\JoseBundle\Controller\JWKSetController');
            $controller_definition->setFactory([new Reference('jose.controller.jwkset_controllery_factory'), 'createJWKSetController']);
            $controller_definition->setArguments([new Reference($id)]);
            $controller_id = 'jose.controller.'.$id;
            $container->setDefinition($controller_id, $controller_definition);
            
            $jwkset_loader_definition = $container->getDefinition('jose.routing.jwkset_loader');
            $jwkset_loader_definition->addMethodCall('addJWKSetRoute', [$config['path'], $controller_id]);
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
                ->scalarNode('path')
                    ->info('To share the JWKSet, then set a valid path (e.g. "/jwkset.json").')
                    ->defaultNull()
                ->end()
            ->end();
    }
}
