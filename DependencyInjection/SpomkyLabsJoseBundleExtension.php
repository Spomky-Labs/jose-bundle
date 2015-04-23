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

        $parameters = array(
            'jwt_class',
            'jws_class',
            'jwe_class',
            'jwk_class',
            'jwkset_class',
            'algorithms',
            'compression_methods',
            'server_name',
            'serialization_mode',
        );
        $aliases = array(
            'signer',
            'loader',
            'encrypter',
            'jwa_manager',
            'jwt_manager',
            'jwk_manager',
            'jwkset_manager',
            'compression_manager',
        );
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
