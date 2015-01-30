<?php

namespace SpomkyLabs\JoseBundle\Chain;

use Jose\Compression\CompressionInterface;

class CompressionChain
{
    private $compression_methods = array();
    private $compression_methods_enabled = array();

    public function __construct(array $compression_methods_enabled)
    {
        $this->compression_methods_enabled = $compression_methods_enabled;
    }

    public function addCompressionMethod(CompressionInterface $compression_method)
    {
        if (in_array($compression_method->getMethodName(), $this->compression_methods_enabled)) {

            $this->compression_methods[$compression_method->getMethodName()] = $compression_method;
        }
        return $this;
    }

    public function getCompressionMethods()
    {
        return $this->compression_methods;
    }
}
