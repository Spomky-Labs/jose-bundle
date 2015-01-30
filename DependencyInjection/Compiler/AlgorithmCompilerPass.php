<?php

namespace SpomkyLabs\JoseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class AlgorithmCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('spomky_jose.chain.algorithm')) {
            return;
        }

        $definition = $container->getDefinition('spomky_jose.chain.algorithm');

        $taggedServices = $container->findTaggedServiceIds('jose_algorithm');
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall('addAlgorithm', array(new Reference($id)));
        }
    }
}
