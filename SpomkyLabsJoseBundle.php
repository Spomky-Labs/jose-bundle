<?php

namespace SpomkyLabs\JoseBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use SpomkyLabs\JoseBundle\DependencyInjection\SpomkyLabsJoseBundleExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use SpomkyLabs\JoseBundle\DependencyInjection\Compiler\AlgorithmCompilerPass;
use SpomkyLabs\JoseBundle\DependencyInjection\Compiler\CompressionCompilerPass;

class SpomkyLabsJoseBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new SpomkyLabsJoseBundleExtension('spomky_jose');
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AlgorithmCompilerPass());
        $container->addCompilerPass(new CompressionCompilerPass());
    }
}
