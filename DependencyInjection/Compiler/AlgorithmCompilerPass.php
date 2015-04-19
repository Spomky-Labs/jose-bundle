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

        $loaded = array();
        $algorithms_enabled = $container->getParameter('spomky_jose.algorithms');
        $definition = $container->getDefinition('spomky_jose.chain.algorithm');

        $taggedServices = $container->findTaggedServiceIds('jose_algorithm');
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                if (!array_key_exists("alias", $attributes)) {
                    throw new \InvalidArgumentException(sprintf("The algorithm '%s' does not have any 'alias' attribute.", $id));
                }
                if (in_array($attributes["alias"], $algorithms_enabled)) {
                    $loaded[] = $attributes["alias"];
                    $definition->addMethodCall('addAlgorithm', array($attributes["alias"], new Reference($id)));
                }
            }
        }
        $diff = array_diff($algorithms_enabled, $loaded);
        if (!empty($diff)) {
            throw new \InvalidArgumentException(sprintf("The following algorithms do not exist or can not be loaded: %s.", json_encode(array_values($diff))));
        }
    }
}
