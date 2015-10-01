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

use Jose\JWKManagerInterface as Base;

interface JWKManagerInterface extends Base
{
    /**
     * @param string $kid    The key ID
     * @param bool   $public True if the key to find is public, false if the key is private
     *
     * @return \Jose\JWKInterface|null
     */
    public function findKeyById($kid, $public);

    /**
     * @param string $certificate An ECC or RSA key file or the path to a certificate
     * @param string $passphrase  Password if certificate is protected
     *
     * @return \Jose\JWKInterface
     */
    public function loadKeyFromKeyFile($certificate, $passphrase = null);

    /**
     * @param array $values Values of the key
     *
     * @return \Jose\JWKInterface
     */
    public function loadKeyFromValues(array $values);
}
