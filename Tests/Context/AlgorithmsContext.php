<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\Features\Context;

/**
 * Behat context class.
 */
trait AlgorithmsContext
{
    private $algorithm_list = [];

    /**
     * Returns HttpKernel service container.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    abstract protected function getContainer();

    /**
     * @When I list algorithms
     */
    public function iListAlgorithms()
    {
        $this->algorithm_list = $this->getAlgorithmManager()->listAlgorithms();
    }

    /**
     * @Then I should get a non empty list of algorithms
     */
    public function iShouldGetANonEmptyListOfAlgorithms()
    {
        if (empty($this->algorithm_list)) {
            throw new \Exception('No algorithm supported by the algorithm manager');
        }
    }

    /**
     * @return \Jose\Algorithm\JWAManagerInterface
     */
    protected function getAlgorithmManager()
    {
        return $this->getContainer()->get('jose.algorithm_manager');
    }
}
