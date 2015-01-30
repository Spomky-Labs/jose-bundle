<?php

namespace SpomkyLabs\JoseBundle;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use SpomkyLabs\JoseBundle\DependencyInjection\Security\Factory\OAuth2Factory;
use SpomkyLabs\JoseBundle\DependencyInjection\SpomkyLabsJoseBundleExtension;

class SpomkyLabsJoseBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new SpomkyLabsJoseBundleExtension('spomky_jose');
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        if (version_compare(Kernel::VERSION, '2.1', '>=')) {
            $extension = $container->getExtension('security');
            $extension->addSecurityListenerFactory(new OAuth2Factory());
        } else {
            throw \Exception("Unsupported Symfony Version");
        }
    }
}
