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
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class SpomkyLabsJoseBundleExtension extends Extension
{
    /**
     * @var \SpomkyLabs\JoseBundle\DependencyInjection\Source\JWKSetSource\JWKSetSourceInterface[]
     */
    private $jwk_set_sources;

    /**
     * @var string
     */
    private $alias;

    /**
     * @var \SpomkyLabs\JoseBundle\DependencyInjection\Source\SourceInterface[]
     */
    private $service_sources = [];

    /**
     * @param string $alias
     */
    public function __construct($alias)
    {
        $this->alias = $alias;
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
        $this->updateSources();

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

    /**
     * 
     */
    private function updateSources()
    {
        $this->addServiceSource(new JWTCreatorSource());
        $this->addServiceSource(new JWTLoaderSource());
        $this->addServiceSource(new SignerSource());
        $this->addServiceSource(new VerifierSource());
        $this->addServiceSource(new EncrypterSource());
        $this->addServiceSource(new DecrypterSource());
        $this->addServiceSource(new CheckerSource());
        $this->addServiceSource(new JWKSource());
        $this->addServiceSource(new JWKSetSource());
    }
}
