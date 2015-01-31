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

        $loaded = array();
        $compression_methods_enabled = $container->getParameter('spomky_jose.compression_methods');
        $definition = $container->getDefinition('spomky_jose.chain.compression');

        $taggedServices = $container->findTaggedServiceIds('jose_compression');
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                if (!array_key_exists("alias", $attributes)) {
                    throw new \InvalidArgumentException(sprintf("The compression method '%s' does not have any 'alias' attribute.", $id));
                }
                if (in_array($attributes["alias"], $compression_methods_enabled)) {
                    $loaded[] = $attributes["alias"];
                    $definition->addMethodCall('addCompressionMethod', array(new Reference($id)));
                }
            }
        }
        $diff = array_diff($compression_methods_enabled, $loaded);
        if (!empty($diff)) {
            throw new \InvalidArgumentException(sprintf("The following compression methods do not exist or can not be loaded: %s.", json_encode(array_values($diff))));
        }
    }
}
