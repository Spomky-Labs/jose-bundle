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

use Jose\Compression\CompressionInterface;

class CompressionChain
{
    private $compression_methods = [];

    public function addCompressionMethod(CompressionInterface $compression_method)
    {
        $this->compression_methods[$compression_method->getMethodName()] = $compression_method;

        return $this;
    }

    public function getCompressionMethods()
    {
        return $this->compression_methods;
    }
}
