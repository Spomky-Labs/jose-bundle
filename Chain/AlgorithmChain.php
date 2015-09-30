<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\Chain;

use Jose\JWAInterface;
use Psr\Log\LoggerInterface;

class AlgorithmChain
{
    private $logger;
    private $algorithms = [];

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    public function addAlgorithm($alias, JWAInterface $algorithm)
    {
        if ('none' === $alias && !is_null($this->logger)) {
            $this->logger->alert('Algorithm "none" enabled. THIS ALGORITHM IS INSECURE. DO NOT USE IN PRODUCTION!');
        }
        $this->algorithms[$algorithm->getAlgorithmName()] = $algorithm;

        return $this;
    }

    public function getAlgorithms()
    {
        return $this->algorithms;
    }
}
