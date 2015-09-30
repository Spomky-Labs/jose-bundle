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

use Jose\Compression\CompressionManagerInterface;
use SpomkyLabs\JoseBundle\Chain\CompressionChain;

class CompressionManager implements CompressionManagerInterface
{
    protected $compression_methods = [];

    public function __construct(CompressionChain $chain)
    {
        $this->compression_methods = $chain->getCompressionMethods();
    }

    public function getCompressionAlgorithm($name)
    {
        return array_key_exists($name, $this->compression_methods) ? $this->compression_methods[$name] : null;
    }

    public function listCompressionAlgorithm()
    {
        return array_keys($this->compression_methods);
    }
}
