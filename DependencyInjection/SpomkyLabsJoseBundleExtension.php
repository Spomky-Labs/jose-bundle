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

use Assert\Assertion;
use SpomkyLabs\JoseBundle\DependencyInjection\Source\CheckerSource;
use SpomkyLabs\JoseBundle\DependencyInjection\Source\DecrypterSource;
use SpomkyLabs\JoseBundle\DependencyInjection\Source\EasyJWTCreatorSource;
use SpomkyLabs\JoseBundle\DependencyInjection\Source\EasyJWTLoaderSource;
use SpomkyLabs\JoseBundle\DependencyInjection\Source\EncrypterSource;
use SpomkyLabs\JoseBundle\DependencyInjection\Source\JWKSetSource;
use SpomkyLabs\JoseBundle\DependencyInjection\Source\JWKSource;
use SpomkyLabs\JoseBundle\DependencyInjection\Source\JWTCreatorSource;
use SpomkyLabs\JoseBundle\DependencyInjection\Source\JWTLoaderSource;
use SpomkyLabs\JoseBundle\DependencyInjection\Source\SignerSource;
use SpomkyLabs\JoseBundle\DependencyInjection\Source\SourceInterface;
use SpomkyLabs\JoseBundle\DependencyInjection\Source\VerifierSource;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class SpomkyLabsJoseBundleExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @var string
     */
    private $alias;
    
    /**
     * @var string
     */
    private $bundle_path;

    /**
     * @var \SpomkyLabs\JoseBundle\DependencyInjection\Source\SourceInterface[]
     */
    private $service_sources = [];

    /**
     * SpomkyLabsJoseBundleExtension constructor.
     *
     * @param string $alias
     * @param string $bundle_path
     */
    public function __construct($alias, $bundle_path)
    {
        $this->alias = $alias;
        $this->bundle_path = $bundle_path;
        $this->updateSources();
    }

    /**
     * @param \SpomkyLabs\JoseBundle\DependencyInjection\Source\SourceInterface $source
     */
    public function addServiceSource(SourceInterface $source)
    {
        $name = $source->getName();
        Assertion::false(in_array($name, $this->service_sources), sprintf('The source "%s" is already set.', $name));
        $this->service_sources[$name] = $source;
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
        $services = ['services', 'compression_methods', 'checkers', 'signature_algorithms', 'encryption_algorithms', 'checkers'];
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
        return new Configuration($this->getAlias(), $this->service_sources);
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
        foreach ($this->service_sources as $service_source) {
            foreach ($config[$service_source->getName()] as $name => $data) {
                $service_source->createService($name, $data, $container);
            }
        }
    }


    private function updateSources()
    {
        $this->addServiceSource(new JWTCreatorSource());
        $this->addServiceSource(new JWTLoaderSource());
        $this->addServiceSource(new SignerSource());
        $this->addServiceSource(new VerifierSource());
        $this->addServiceSource(new EncrypterSource());
        $this->addServiceSource(new DecrypterSource());
        $this->addServiceSource(new CheckerSource());
        $this->addServiceSource(new JWKSource($this->bundle_path));
        $this->addServiceSource(new JWKSetSource($this->bundle_path));
        $this->addServiceSource(new EasyJWTCreatorSource());
        $this->addServiceSource(new EasyJWTLoaderSource());
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        foreach ($this->service_sources as $service_source) {
            $result = $service_source->prepend($container, $config);
            if (null !== $result) {
                $container->prependExtensionConfig($this->getAlias(), $result);
            }
        }
    }
}
