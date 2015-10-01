<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SpomkyLabsJoseBundleExtension extends Extension
{
    private $alias;

    /**
     * @param string $alias
     */
    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration($this->getAlias());

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $services = ['services', 'signature_algorithms', 'encryption_algorithms', 'compression_methods', 'checkers', 'payload_converters'];
        if (true === $config['use_controller']) {
            $services[] = 'jwkset_controller';
        }
        foreach ($services as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }

        $parameters = [
            'jot',
            'keys',
            'algorithms',
            'compression_methods',
            'server_name',
        ];
        $aliases = [
            'jwt_manager',
            'jwk_manager',
            'jwkset_manager',
        ];
        foreach ($parameters as $parameter) {
            $container->setParameter($this->getAlias().'.'.$parameter, $config[$parameter]);
        }
        foreach ($aliases as $alias) {
            $container->setAlias($this->getAlias().'.'.$alias, $config[$alias]);
        }
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }
}
