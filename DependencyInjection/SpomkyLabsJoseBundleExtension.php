<?php

namespace SpomkyLabs\JoseBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

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
        $processor     = new Processor();
        $configuration = new Configuration($this->getAlias());

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        foreach (array('services', 'chain', 'controllers', 'signature_algorithms', 'encryption_algorithms', 'compression_methods') as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }

        $container->setParameter($this->getAlias().'.algorithms', $config['algorithms']);
        $container->setParameter($this->getAlias().'.compression_methods', $config['compression_methods']);
        $container->setParameter($this->getAlias().'.server_name', $config['server_name']);
        $container->setParameter($this->getAlias().'.serialization_mode', $config['serialization_mode']);
        $container->setAlias($this->getAlias().'.jwa_manager', $config['jwa_manager']);
        $container->setAlias($this->getAlias().'.jwt_manager', $config['jwt_manager']);
        $container->setAlias($this->getAlias().'.jwk_manager', $config['jwk_manager']);
        $container->setAlias($this->getAlias().'.compression_manager', $config['compression_manager']);
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }
}
