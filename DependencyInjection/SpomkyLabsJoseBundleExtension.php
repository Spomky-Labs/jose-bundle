<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2016 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\DependencyInjection;

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
     * @var \SpomkyLabs\JoseBundle\DependencyInjection\JWKSetSource\JWKSetSourceInterface[]
     */
    private $jwk_set_sources;

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

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $services = $this->getXmlFileToLoad();
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
        $jwk_sources = $this->createJWKSources();
        $jwk_set_sources = $this->createJWKSetSources();

        return new Configuration($this->getAlias(), $jwk_sources, $jwk_set_sources);
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
        foreach ($config['keys'] as $name => $key) {
            $this->createJWK($name, $key, $container, $this->jwk_sources);
        }

        foreach ($config['key_sets'] as $name => $key_set) {
            $this->createJWKSet($name, $key_set, $container, $this->jwk_set_sources);
        }

        foreach ($config['encrypters'] as $name => $encrypter) {
            $this->createEncrypter($name, $encrypter, $container);
        }

        foreach ($config['decrypters'] as $name => $decrypter) {
            $this->createDecrypter($name, $decrypter, $container);
        }

        foreach ($config['signers'] as $name => $signer) {
            $this->createSigner($name, $signer, $container);
        }

        foreach ($config['verifiers'] as $name => $verifier) {
            $this->createVerifier($name, $verifier, $container);
        }
    }

    /**
     * @param string                                                                       $name
     * @param array                                                                        $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder                      $container
     * @param \SpomkyLabs\JoseBundle\DependencyInjection\JWKSetSource\JWKSetSourceInterface[] $jwk_set_sources
     */
    private function createJWKSet($name, array $config, ContainerBuilder $container, array $jwk_set_sources)
    {
        foreach ($config as $key => $adapter) {
            if (array_key_exists($key, $jwk_set_sources)) {
                $service_id = sprintf('jose.key_set.%s', $name);
                $jwk_set_sources[$key]->create($container, $service_id, $adapter);

                return;
            }
        }
        throw new \LogicException(sprintf('The JWKSet definition "%s" is not configured.', $name));
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
                $service_id = sprintf('jose.key.%s', $name);
                $jwk_sources[$key]->create($container, $service_id, $adapter);

                return;
            }
        }
        throw new \LogicException(sprintf('The JWK definition "%s" is not configured.', $name));
    }

    /**
     * @param string                                                  $name
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function createEncrypter($name, array $config, ContainerBuilder $container)
    {
        $service_id = sprintf('jose.encrypter.%s', $name);
        $definition = new Definition('Jose\Encrypter');
        $definition->setFactory([
            new Reference('jose.factory.service'),
            'createEncrypter',
        ]);
        $definition->setArguments([
            $config['algorithms'],
            $config['compression_methods'],
            null === $config['logger'] ? null : new Reference($config['logger']),
        ]);

        $container->setDefinition($service_id, $definition);
        
        if (true === $config['create_decrypter']) {
            $this->createDecrypter($name, $config, $container);
        }
    }

    /**
     * @param string                                                  $name
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function createDecrypter($name, array $config, ContainerBuilder $container)
    {
        $service_id = sprintf('jose.decrypter.%s', $name);
        $definition = new Definition('Jose\Decrypter');
        $definition->setFactory([
            new Reference('jose.factory.service'),
            'createDecrypter',
        ]);
        $definition->setArguments([
            $config['algorithms'],
            $config['compression_methods'],
            null === $config['logger'] ? null : new Reference($config['logger']),
        ]);
        
        $container->setDefinition($service_id, $definition);
    }

    /**
     * @param string                                                  $name
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function createSigner($name, array $config, ContainerBuilder $container)
    {
        $service_id = sprintf('jose.signer.%s', $name);
        $definition = new Definition('Jose\Signer');
        $definition->setFactory([
            new Reference('jose.factory.service'),
            'createSigner',
        ]);
        $definition->setArguments([
            $config['algorithms'],
            null === $config['logger'] ? null : new Reference($config['logger']),
        ]);

        $container->setDefinition($service_id, $definition);
        
        if (true === $config['create_verifier']) {
            $this->createVerifier($name, $config, $container);
        }
    }

    /**
     * @param string                                                  $name
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function createVerifier($name, array $config, ContainerBuilder $container)
    {
        $service_id = sprintf('jose.verifier.%s', $name);
        $definition = new Definition('Jose\Verifier');
        $definition->setFactory([
            new Reference('jose.factory.service'),
            'createVerifier',
        ]);
        $definition->setArguments([
            $config['algorithms'],
            null === $config['logger'] ? null : new Reference($config['logger']),
        ]);
        
        $container->setDefinition($service_id, $definition);
    }

    /**
     * @return string[]
     */
    private function getXmlFileToLoad()
    {
        $services = [
            'services',
            'compression_methods',
            'checkers',
            'signature_algorithms',
            'encryption_algorithms',
            'checkers',
        ];

        return $services;
    }

    private function createJWKSources()
    {
        if (null !== $this->jwk_sources) {
            return $this->jwk_sources;
        }

        // load bundled adapter factories
        $tempContainer = new ContainerBuilder();
        $loader = new XmlFileLoader($tempContainer, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('jwk_sources.xml');

        $services = $tempContainer->findTaggedServiceIds('jose.jwk_source');
        $jwk_sources = [];
        foreach (array_keys($services) as $id) {
            $factory = $tempContainer->get($id);
            $jwk_sources[str_replace('-', '_', $factory->getKey())] = $factory;
        }

        return $this->jwk_sources = $jwk_sources;
    }

    private function createJWKSetSources()
    {
        if (null !== $this->jwk_set_sources) {
            return $this->jwk_set_sources;
        }

        // load bundled adapter factories
        $tempContainer = new ContainerBuilder();
        $loader = new XmlFileLoader($tempContainer, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('jwk_set_sources.xml');

        $services = $tempContainer->findTaggedServiceIds('jose.jwk_set_source');
        $jwk_set_sources = [];
        foreach (array_keys($services) as $id) {
            $factory = $tempContainer->get($id);
            $jwk_set_sources[str_replace('-', '_', $factory->getKeySet())] = $factory;
        }

        return $this->jwk_set_sources = $jwk_set_sources;
    }
}
