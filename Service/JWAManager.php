<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\Service;

use Jose\JWAManager as Base;
use SpomkyLabs\JoseBundle\Chain\AlgorithmChain;

class JWAManager extends Base
{
    protected $algorithms = [];

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
