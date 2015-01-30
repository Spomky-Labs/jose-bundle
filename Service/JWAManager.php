<?php

namespace SpomkyLabs\JoseBundle\Service;

use Jose\JWAManager as Base;
use SpomkyLabs\JoseBundle\Chain\AlgorithmChain;

class JWAManager extends Base
{
    protected $algorithms = array();

    public function __construct(AlgorithmChain $chain)
    {
        $this->algorithms = $chain->getAlgorithms();
    }

    public function getAlgorithm($algorithm)
    {
        return array_key_exists($algorithm, $this->algorithms) ? $this->algorithms[$algorithm] : null;
    }

    public function getAlgorithms()
    {
        return $this->algorithms;
    }

    public function listAlgorithms()
    {
        return array_keys($this->getAlgorithms());
    }
}
