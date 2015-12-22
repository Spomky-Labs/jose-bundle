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

final class SpomkyLabsJoseBundleExtension extends Extension
{
    /**
     * @var string
     */
    private $alias;

    /**
     * @param string $alias
     */
    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration($this->getAlias());

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $services = $this->getXmlFileToLoad();
        if (true === $config['jot']['enabled']) {
            $services[] = 'jot';
            $container->setParameter($this->getAlias().'.jot.class', $config['jot']['class']);
            $container->setAlias($this->getAlias().'.jot.manager', $config['jot']['manager']);
        }
        foreach ($services as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }

        $parameters = [
            'server_name',
            'compression_methods',
        ];

        foreach ($parameters as $parameter) {
            $container->setParameter($this->getAlias().'.'.$parameter, $config[$parameter]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return string[]
     */
    private function getXmlFileToLoad()
    {
        return [
            'services',
            'compression_methods',
            'checkers',
            'payload_converters',
        ];
    }
}
