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

final class AlgorithmCompilerPass implements CompilerPassInterface
{
    private function getSupportedKeywords()
    {
        return [
            'required',
            'recommended+',
            'recommended',
            'recommended-',
            'optional',
        ];
    }

    private function removeKeywordAlgorithms(array &$algorithms_enabled, array &$available_algorithms, array &$algorithms_loaded)
    {
        foreach ($algorithms_enabled as $key => $algorithm) {
            if ('!' === substr($algorithm, 0, 1)) {
                $keyword = substr($algorithm, 1, strlen($algorithm) - 1);
                if (in_array($keyword, $this->getSupportedKeywords()) && array_key_exists($keyword, $available_algorithms)) {
                    foreach ($available_algorithms[$keyword] as $algo) {
                        if (array_key_exists($algorithm, $algo)) {
                            unset($algorithms_loaded[$algorithm]);
                        }
                    }
                    unset($algorithms_enabled[$key]);
                }
            }
        }
    }

    private function removeAliasAlgorithms(array &$algorithms_enabled, array &$algorithms_loaded)
    {
        foreach ($algorithms_enabled as $key => $algorithm) {
            if ('!' === substr($algorithm, 0, 1)) {
                if (true === array_key_exists(substr($algorithm, 1, strlen($algorithm) - 1), $algorithms_loaded)) {
                    unset($algorithms_loaded[substr($algorithm, 1, strlen($algorithm) - 1)]);
                }
                unset($algorithms_enabled[$key]);
            }
        }
    }

    private function addAliasAlgorithms(array &$algorithms_enabled, array &$available_algorithms, array &$algorithms_loaded)
    {
        foreach ($algorithms_enabled as $key => $algorithm) {
            foreach ($available_algorithms as $algo) {
                if (array_key_exists($algorithm, $algo)) {
                    $algorithms_loaded[$algorithm] = $algo[$algorithm];
                    unset($algorithms_enabled[$key]);
                    break;
                }
            }
        }
    }

    private function addKeywordAlgorithms(array &$algorithms_enabled, array &$available_algorithms, array &$algorithms_loaded)
    {
        foreach ($this->getSupportedKeywords() as $key => $keyword) {
            if (in_array($keyword, $algorithms_enabled) && array_key_exists($keyword, $available_algorithms)) {
                $algorithms_loaded = array_merge(
                    $algorithms_loaded,
                    $available_algorithms[$keyword]
                );
                unset($algorithms_enabled[$key]);
            }
        }
    }

    private function processKeywords(array &$algorithms_enabled)
    {
        if (false !== ($pos = array_search('all', $algorithms_enabled))) {
            unset($algorithms_enabled[$pos]);
            $algorithms_enabled = array_merge(
                $this->getSupportedKeywords(),
                $algorithms_enabled
            );
        }
    }

    private function checkLoadedAlgorithms(array $algorithms_enabled, array $algorithms_loaded)
    {
        $diff = array_diff($algorithms_enabled, $algorithms_loaded);
        if (!empty($diff)) {
            throw new \InvalidArgumentException(sprintf('The following algorithms do not exist or can not be loaded: %s.', json_encode(array_values($diff))));
        }
    }

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('jose.algorithm_manager')) {
            return;
        }

        $available_algorithms = $this->getAvailableAlgorithms($container);
        $algorithms_enabled = $container->getParameter('jose.algorithms');
        $algorithms_loaded = [];

        $this->processKeywords($algorithms_enabled);
        $this->addKeywordAlgorithms($algorithms_enabled, $available_algorithms, $algorithms_loaded);
        $this->addAliasAlgorithms($algorithms_enabled, $available_algorithms, $algorithms_loaded);
        $this->removeKeywordAlgorithms($algorithms_enabled, $available_algorithms, $algorithms_loaded);
        $this->removeAliasAlgorithms($algorithms_enabled, $algorithms_loaded);
        $this->checkLoadedAlgorithms($algorithms_enabled, $algorithms_loaded);

        $this->loadAlgorithms($container, $algorithms_loaded);
    }

    private function getAvailableAlgorithms(ContainerBuilder $container)
    {
        $result = [];
        $taggedServices = $container->findTaggedServiceIds('jose_algorithm');
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                foreach(['alias', 'requirement', 'category'] as $attr) {
                    if (!array_key_exists($attr, $attributes)) {
                        throw new \InvalidArgumentException(sprintf("The algorithm '%s' does not have any '%s' attribute.", $id, $attr));
                    }
                }
                $result[$attributes['requirement']][$attributes['alias']] = $id;
            }
        }

        return $result;
    }

    private function loadAlgorithms(ContainerBuilder $container, array $algorithms)
    {
        $definition = $container->getDefinition('jose.algorithm_manager');
        foreach ($algorithms as $alias => $id) {
            $definition->addMethodCall('addAlgorithm', [new Reference($id)]);
        }
    }
}
