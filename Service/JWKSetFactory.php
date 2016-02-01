<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2016 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\Service;

use Jose\Object\JWKSet;

final class JWKSetFactory
{
    /**
     * @param \Jose\Object\JWKInterface[] $keys
     *
     * @return \Jose\Object\JWKSetInterface
     */
    public static function createFromKey(array $keys)
    {
        $jwk_set = new JWKSet();
        foreach ($keys as $key) {
            $jwk_set = $jwk_set->addKey($key);
        }

        return $jwk_set;
    }
}
