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

use SpomkyLabs\Jose\JWKManager as Base;
use SpomkyLabs\Jose\KeyConverter\KeyConverter;

class JWKManager extends Base implements JWKManagerInterface
{
    /**
     * {@inheritdoc}()
     */
    public function loadKeyFromKeyFile($certificate, $passphrase = null)
    {
        $values = KeyConverter::loadKeyFromFile($certificate, $passphrase);

        return $this->createJWK($values);
    }

    /**
     * {@inheritdoc}()
     */
    public function loadKeyFromValues(array $values)
    {
        return $this->createJWK($values);
    }

    public function findKeyById($kid, $public)
    {
    }
}
