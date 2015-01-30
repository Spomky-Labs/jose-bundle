<?php

namespace SpomkyLabs\JoseBundle\Service;

use Jose\Compression\CompressionManagerInterface;
use SpomkyLabs\JoseBundle\Chain\CompressionChain;

class CompressionManager implements CompressionManagerInterface
{
    protected $compression_methods = array();

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
