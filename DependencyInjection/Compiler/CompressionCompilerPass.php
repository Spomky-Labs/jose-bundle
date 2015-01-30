<?php

namespace SpomkyLabs\JoseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class CompressionCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('spomky_jose.chain.compression')) {
            return;
        }

        $definition = $container->getDefinition('spomky_jose.chain.compression');

        $taggedServices = $container->findTaggedServiceIds('jose_compression');
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall('addCompressionMethod', array(new Reference($id)));
        }
    }
}
