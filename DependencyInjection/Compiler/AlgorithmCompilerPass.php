<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AlgorithmCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sl_jose.chain.algorithm')) {
            return;
        }

        $keywords = [
            'required'     => 'getRequiredAlgorithms',
            'recommended+' => 'getRecommendedPlusAlgorithms',
            'recommended'  => 'getRecommendedAlgorithms',
            'recommended-' => 'getRecommendedMinusAlgorithms',
            'optional'     => 'getOptionalAlgorithms',
            'all'          => 'getAllAlgorithms',
        ];

        $algorithms = [];
        $algorithms_enabled = $container->getParameter('sl_jose.algorithms');
        foreach ($keywords as $key => $method) {
            if (in_array($key, $algorithms_enabled)) {
                $algorithms = array_merge(
                    $algorithms,
                    $this->$method($container)
                );
                $array_keys = array_keys($algorithms_enabled, $key);
                foreach ($array_keys as $array_key) {
                    unset($algorithms_enabled[$array_key]);
                }
            }
        }
        $algorithms = array_merge(
            $algorithms,
            $this->getAlgorithmsFromAliases($container, $algorithms_enabled)
        );

        if (in_array('!none', $algorithms_enabled) && array_key_exists('none', $algorithms)) {
            unset($algorithms['none']);
        }
        $this->loadAlgorithms($container, $algorithms);
    }

    private function getRequiredAlgorithms(ContainerBuilder $container)
    {
        return $this->getAlgorithmsFromRequirement($container, 'required');
    }

    private function getRecommendedPlusAlgorithms(ContainerBuilder $container)
    {
        return $this->getAlgorithmsFromRequirement($container, 'recommended+');
    }

    private function getRecommendedAlgorithms(ContainerBuilder $container)
    {
        return $this->getAlgorithmsFromRequirement($container, 'recommended');
    }

    private function getRecommendedMinusAlgorithms(ContainerBuilder $container)
    {
        return $this->getAlgorithmsFromRequirement($container, 'recommended-');
    }

    private function getOptionalAlgorithms(ContainerBuilder $container)
    {
        return $this->getAlgorithmsFromRequirement($container, 'optional');
    }

    private function getAllAlgorithms(ContainerBuilder $container)
    {
        return $this->getAlgorithmsFromRequirement($container, null);
    }

    private function getAlgorithmsFromRequirement(ContainerBuilder $container, $requirement = null)
    {
        $result = [];
        $taggedServices = $container->findTaggedServiceIds('jose_algorithm');
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                if (!array_key_exists('alias', $attributes)) {
                    throw new \InvalidArgumentException(sprintf("The algorithm '%s' does not have any 'alias' attribute.", $id));
                }
                if (!array_key_exists('requirement', $attributes)) {
                    throw new \InvalidArgumentException(sprintf("The algorithm '%s' does not have any 'requirement' attribute.", $id));
                }
                if (is_null($requirement) || $requirement === $attributes['requirement']) {
                    $result[$attributes['alias']] = $id;
                }
            }
        }

        return $result;
    }

    private function getAlgorithmsFromAliases(ContainerBuilder $container, array $aliases)
    {
        $loaded = ['!none'];
        $result = [];
        $taggedServices = $container->findTaggedServiceIds('jose_algorithm');
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                if (!array_key_exists('alias', $attributes)) {
                    throw new \InvalidArgumentException(sprintf("The algorithm '%s' does not have any 'alias' attribute.", $id));
                }
                if (!array_key_exists('requirement', $attributes)) {
                    throw new \InvalidArgumentException(sprintf("The algorithm '%s' does not have any 'requirement' attribute.", $id));
                }
                if (in_array($attributes['alias'], $aliases)) {
                    $loaded[] = $attributes['alias'];
                    $result[$attributes['alias']] = $id;
                }
            }
        }
        $diff = array_diff($aliases, $loaded);
        if (!empty($diff)) {
            throw new \InvalidArgumentException(sprintf('The following algorithms do not exist or can not be loaded: %s.', json_encode(array_values($diff))));
        }

        return $result;
    }

    private function loadAlgorithms(ContainerBuilder $container, array $algorithms)
    {
        $definition = $container->getDefinition('sl_jose.chain.algorithm');
        foreach ($algorithms as $alias => $id) {
            $definition->addMethodCall('addAlgorithm', [$alias, new Reference($id)]);
        }
    }
}
