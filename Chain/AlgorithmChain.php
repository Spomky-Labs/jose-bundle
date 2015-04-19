<?php

namespace SpomkyLabs\JoseBundle\Chain;

use Jose\JWAInterface;
use Psr\Log\LoggerInterface;

class AlgorithmChain
{
    private $logger;
    private $algorithms = array();

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
