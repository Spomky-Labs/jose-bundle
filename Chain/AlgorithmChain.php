<?php

namespace SpomkyLabs\JoseBundle\Chain;

use Jose\JWAInterface;

class AlgorithmChain
{
    private $algorithms = array();

    public function addAlgorithm(JWAInterface $algorithm)
    {
        $this->algorithms[$algorithm->getAlgorithmName()] = $algorithm;

        return $this;
    }

    public function getAlgorithms()
    {
        return $this->algorithms;
    }
}
