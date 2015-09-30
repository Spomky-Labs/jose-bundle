<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

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
