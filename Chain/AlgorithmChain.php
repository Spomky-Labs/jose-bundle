<?php

namespace SpomkyLabs\JoseBundle\Chain;

use Jose\JWAInterface;

class AlgorithmChain
{
    private $algorithms = array();
    private $algorithms_enabled = array();

    public function __construct(array $algorithms_enabled)
    {
        $this->algorithms_enabled = $algorithms_enabled;
    }

    public function addAlgorithm(JWAInterface $algorithm)
    {
        if (in_array($algorithm->getAlgorithmName(), $this->algorithms_enabled)) {
            $this->algorithms[$algorithm->getAlgorithmName()] = $algorithm;
        }
        return $this;
    }

    public function getAlgorithms()
    {
        return $this->algorithms;
    }
}
