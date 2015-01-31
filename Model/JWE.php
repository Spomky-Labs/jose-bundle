<?php

namespace SpomkyLabs\JoseBundle\Model;

use Jose\JWEInterface;

class JWE extends JWT implements JWEInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEncryptionAlgorithm()
    {
        return $this->getHeaderValue('enc');
    }

    /**
     * {@inheritdoc}
     */
    public function getZip()
    {
        return $this->getHeaderValue('zip');
    }
}
