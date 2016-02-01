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

use Jose\Object\JWKSet;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class SpomkyLabsJoseBundleExtension extends Extension
{
    /**
     * @var \SpomkyLabs\JoseBundle\DependencyInjection\JWKSource\JWKSourceInterface[]
     */
    private $jwk_sources;

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

        $config = $processor->processConfiguration(
            $this->getConfiguration($configs, $container),
            $configs
        );

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $services = $this->getXmlFileToLoad($config);
        foreach ($services as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }

        $this->initConfiguration($container, $config);
    }


    /**
     * {@inheritdoc}
     */
    public function getConfiguration(array $configs, ContainerBuilder $container)
    {
        $jwk_sources  = $this->createJWKSources();

        return new Configuration($this->getAlias(), $jwk_sources);
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array                                                   $config
     */
    private function initConfiguration(ContainerBuilder $container, array $config)
    {
        if (true === $config['storage']['enabled']) {
            $container->setParameter($this->getAlias() . '.jot.class', $config['storage']['class']);
            $container->setAlias($this->getAlias() . '.jot.manager', $config['storage']['manager']);
        }

        $parameters = [
            'compression_methods',
        ];

        foreach ($parameters as $parameter) {
            $container->setParameter($this->getAlias() . '.' . $parameter, $config[$parameter]);
        }

        foreach ($config['keys'] as $name => $key) {
            $this->createJWK($name, $key, $container, $this->jwk_sources);
        }

        foreach ($config['key_sets'] as $name => $key_set) {
            $this->createJWKSet($name, $key_set, $container);
        }
    }

    /**
     * @param string                                                                    $name
     * @param array                                                                     $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder                   $container
     */
    private function createJWKSet($name, array $config, ContainerBuilder $container)
    {
        $keys = [];
        foreach ($config as $kid) {
            $id = sprintf('jose.key.%s', $kid);

            $keys[] = new Reference($id);
        }

        $service_id = sprintf('jose.key_set.%s', $name);
        $definition = new Definition('Jose\Object\JWKSet');
        $definition->setFactory([
            new Reference('jose.factory.jwk_set'),
            'createFromKey'
        ]);
        $definition->setArguments([
            $keys,
        ]);

        $container->setDefinition($service_id, $definition);
    }

    /**
     * @param string                                                                    $name
     * @param array                                                                     $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder                   $container
     * @param \SpomkyLabs\JoseBundle\DependencyInjection\JWKSource\JWKSourceInterface[] $jwk_sources
     */
    private function createJWK($name, array $config, ContainerBuilder $container, array $jwk_sources)
    {
        foreach ($config as $key => $adapter) {
            if (array_key_exists($key, $jwk_sources)) {
                $jwk_sources[$key]->create($container, $name, $adapter);

                return;
            }
        }
        throw new \LogicException(sprintf('The JWK definition "%s" is not configured.', $name));
    }

    /**
     * @param array $config
     *
     * @return string[]
     */
    private function getXmlFileToLoad(array $config)
    {
        $services = [
            'services',
            'compression_methods',
            'checkers',
        ];

        if (true === $config['storage']['enabled']) {
            $services[] = 'jot';
        }

        return $services;
    }

    private function createJWKSources()
    {
        if (null !== $this->jwk_sources) {
            return $this->jwk_sources;
        }

        // load bundled adapter factories
        $tempContainer = new ContainerBuilder();
        $loader = new XmlFileLoader($tempContainer, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('jwk_sources.xml');

        $services = $tempContainer->findTaggedServiceIds('jose.jwk_source');
        $jwk_sources = array();
        foreach (array_keys($services) as $id) {
            $factory = $tempContainer->get($id);
            $jwk_sources[str_replace('-', '_', $factory->getKey())] = $factory;
        }

        return $this->jwk_sources = $jwk_sources;
    }
}
