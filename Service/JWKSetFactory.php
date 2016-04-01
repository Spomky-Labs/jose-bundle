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

use Jose\Factory\JWKFactory;
use Jose\Object\JWKSet;

final class JWKSetFactory
{
    /**
     * @param \Jose\Object\JWKInterface[] $keys
     *
     * @return \Jose\Object\JWKSetInterface
     */
    public static function createFromKeys(array $keys)
    {
        $jwk_set = new JWKSet();
        foreach ($keys as $key) {
            $jwk_set = $jwk_set->addKey($key);
        }

        return $jwk_set;
    }

    /**
     * @param string $url
     * @param bool   $allow_unsecured_connection
     *
     * @return \Jose\Object\JWKSetInterface
     */
    public static function createFromJKU($url, $allow_unsecured_connection = false)
    {
        return JWKFactory::createFromJKU($url, $allow_unsecured_connection);
    }

    /**
     * @param string $url
     * @param bool   $allow_unsecured_connection
     *
     * @return \Jose\Object\JWKSetInterface
     */
    public static function createFromX5U($url, $allow_unsecured_connection = false)
    {
        return JWKFactory::createFromX5U($url, $allow_unsecured_connection);
    }

    /**
     * @param array $values
     *
     * @return \Jose\Object\JWKSetInterface
     */
    public static function createFromValues(array $values)
    {
        return new JWKSet($values);
    }
}
