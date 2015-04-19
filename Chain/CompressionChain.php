<?php

namespace SpomkyLabs\JoseBundle\Chain;

use Jose\Compression\CompressionInterface;

class CompressionChain
{
    private $compression_methods = array();

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
